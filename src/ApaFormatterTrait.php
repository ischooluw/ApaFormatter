<?php

namespace Booellean\ApaFormatter;

trait ApaFormatterTrait {

    /////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////
    // Shorthand Functions by Type
    ///////////////////////////////////////////////////////
    //////////////////////////////////////////////////////


    // Educational Game
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    /* If game is not available online */
    ////// Company. or Lastname, F.M. (year). \i  Game name [Type of Game]. Pub City, PS: Publisher. Platform.
    //
    /* If game can be found online */
    ////// Company. or Lastname, F.M. (year). \i Game name [Type of Game]. Retrieved from website_url
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatVideoGame($pub, $params = []){
        $defaultParams = array_merge(['include_url_preface' => true], $params);
        // Get Start Title
        $citation = $this->apaStructureAuthors($pub['authors']);

        // If editors
        if(count($pub['editors']) > 0) $citation .= '& ' . $this->apaGetEditors($pub['editors']);

        // (year)
        $citation .= $this->apaGetYear($pub['year_published']);

        // Video Game name [Video game].
        $citation .= ' ' . $this->apaGetTitle($pub['title']) . $this->apaGetType($pub['type']);

        // TODO: Currently no data field for a platform.
        // Pub City, PS: Publisher. Platform.
        $citation .= $this->apaGetPublishing($pub['pub_city_state'], $pub['publisher']);

        // Retrieved from website_url
        $citation .= $this->apaGetUrl($pub['url'], $defaultParams['include_url_preface']);

        return $citation;
    }

    // Foreword
    // Invited Foreword, Scholarly Book-New
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    ////// Lastname, F.M. (year). Title of Foreword. In Editor(s) or Author of the book, \i Title of Book (pp. page_numbers). Pub City, PS: Publisher.
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatForeword($pub){
        // Get Start Title
        $citation = $this->apaStructureAuthors($pub['authors']);

        // (year)
        $citation .= $this->apaGetYear($pub['year_published']);

        // Title of Foreword
        $citation .= ' ' . $pub['title'] . '.';

        // In Editor(s) or Author of the book,
        if(count($pub['editors']) > 0) $citation .= ' In ' . $this->apaGetEditors($pub['editors']) . ',' ;

        // Title of Book
        $citation .= ' ' . $this->apaGetTitle($pub['title_secondary']);

        // (pp. page_numbers).
        $citation .= $this->apaGetPageNumbers($pub['page_number'], true) . '.';

        // Pub City, PS: Publisher.
        $citation .= $this->apaGetPublishing($pub['pub_city_state'], $pub['publisher']);

        return $citation;
    }

    // Conference Report
    // Report
    // Report Chapter
    // Research Report
    // Technical Report
    // White Paper
    // Working Paper
    // Workshop Report
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    /* If report is not in print */
    ////// Author as Organization. (year, Month). \i Book/Journal Title. Retrieved from website_url
    //
    /* If report is printed */
    ////// Author as Organization. (year, Month). \i Book/Journal Title (edition_num ed.). PubCity, PS: author or Publisher.
    //
    /* If unpublished */
    ////// ... \i Book/Journal Title (Unpublished type) ...
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatReport($pub, $params = []){
        $defaultParams = array_merge(['include_url_preface' => true], $params);
        // Get Start Title
        $citation = $this->apaStructureAuthors($pub['authors']);

        // & Editors...,
        if(count($pub['editors']) > 0) $citation .= '& ' . $this->apaGetEditors($pub['editors']);

        // NOTE: Only have year for now
        // (year)
        $citation .= $this->apaGetYear($pub['year_published']);

        // Book/Journal Title
        $citation .= ' ' . $this->apaGetTitle($pub['title']);

        // Only one of these should ever output. If both output, someone entered something wrong.
        if($pub['status'] !== 'Published'){
            $citation .= ' (Unpublished ' . trim($pub['type']) . ').';
        }else{
            // (edition_num ed.)
            $citation .= $this->apaGetEdition($pub['issue'], '.');
        }

        // In case published with no edition number
        $citation .= $this->apaAddIfElseCharacter([$pub['issue']], '.');

        if($pub['url'] !== '' && $pub['url'] != false){
            // Retrieved from website_url
            $citation .= $this->apaGetUrl($pub['url'], $defaultParams['include_url_preface']);
        }else{
            // Pub City, PS: Publisher.
            $citation .= $this->apaGetPublishing($pub['pub_city_state'], $pub['publisher']);
        }

        return $citation;
    }

    // Software
    // Software, Instructional
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    /* If software name unknown */
    ////// app_developer [software_type]. (year). Retrieved from website_url
    //
    /* If software name known */
    ////// app_developer. (year). App Title [software_type]. Retrieved from website_url
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatSoftware($pub, $params = []){
        $defaultParams = array_merge(['include_url_preface' => true], $params);
        // Get Start Title
        $citation = $this->apaStructureAuthors($pub['authors']);

        // & Editors...,
        if(count($pub['editors']) > 0) $citation .= '& ' . $this->apaGetEditors($pub['editors']);

        // (year). App Title [software_type].
        if($pub['title'] !== '' && $pub['title'] != false){
            $citation .= $this->apaGetYear($pub['year_published']);
            $citation .= ' ' . trim($pub['title']);
            $citation .= ' [' . trim($pub['type']) . '].';

        // [software_type]. (year).
        }else{
            $citation .= ' [' . trim($pub['type']) . '].';
            $citation .= ' ' . $this->apaGetYear($pub['year_published']);
        }

        // Retrieved from website_url
        $citation .= $this->apaGetUrl($pub['url'], $defaultParams['include_url_preface']);

        return $citation;
    }

    // Book Review
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    /* If the review has no name */
    ////// Lastname, F.M. (year). [review_type of \i Book Title, by BookAuthorLastname, F.M.]. Publication Name, volume_number(issue_number), page_numbers. doi:doi_number
    //
    /* If the review has a name */
    ////// Lastname, F.M. (year). Review Title [review_type of \i Book Title, by BookAuthorLastname, F.M.]. Publication Name, volume_number(issue_number). doi:doi_number
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatBookReview($pub){
        // Get Start Title
        $citation = $this->apaStructureAuthors($pub['authors']);

        // (year).
        $citation .= $this->apaGetYear($pub['year_published']);

        // Review Title
        $citation .= $this->apaGetSecondaryTitle($pub['title_secondary']);

        // [review_type of \i Book Title, by BookAuthorLastname, F.M.].
        $citation .= ' [' . $pub['type'] . ' of <em>' . $pub['title'] . '</em>' . ($pub['editors'] != false && $pub['editors'] !== '' && count($pub['editors']) > 0 ? ' by ' . $this->apaGetEditors($pub['editors']) : '') . '].';

        // Publication Name,
        $citation .= ($pub['publisher'] != false && $pub['publisher'] !== '' ? ' ' . $pub['publisher'] . ',' : '');

        // volume_number(issue_number).
        $citation .= $this->apaGetVolumeAndIssue($pub['volume'], $pub['issue'], '.');

        // TODO: No data saved for DOI
        // doi:doi_number

        return $citation;
    }

    // Book Entry, Almanac or Encyclopedia
    // Encyclopedia Entry
    // Update of Book Entry, Almanac or Encyclopedia
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    /* If source is in print */
    ////// Lastname, F.M. (year). Encyclopedia Entry. In EditorLastname, F.M. (Ed.), \i Encyclopedia Name (Vol. volume_number, pp. page_numbers). Pub City, PS: Publisher.
    //
    /* If source is online */
    ////// Encyclopedia Entry. (2006). In \i Encyclopedia Name. Retrieved [Month day, year,] from website_url
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatBookEntry($pub, $params = []){
        $defaultParams = array_merge(['include_url_preface' => true], $params);
        // Get Start Title
        $citation = $this->apaStructureAuthors($pub['authors']);

        // (year).
        $citation .= $this->apaGetYear($pub['year_published']);

        // Encyclopedia Entry.
        $citation .= $this->apaGetSecondaryTitle($pub['title']) . '.';

        // In
        $citation .= ' In ';

        //EditorLastname, F.M. (Ed.)
        if(count($pub['editors']) > 0)$citation .= $this->apaGetEditors($pub['editors']) . ', ';

        // Encyclopedia Name
        $citation .= $this->apaGetTitle($pub['title_secondary']);

        // '' or .
        $char = $this->apaAddIfElseCharacter([
            $pub['volume'],
            $pub['page_number'],
            $pub['pub_city_state'],
            $pub['publisher']
        ], '.', '');
        if($pub['url'] !== '' && $pub['url'] != false){
            $citation .= '.';
        }else{
            $citation .= $char;
        }

        if($pub['url'] !== '' && $pub['url'] != false){
            // NOTE: we can't have a retrieved date, really =_=
            // Retrieved [Month day, year,] from website_url
            $citation .= $this->apaGetUrl($pub['url'], $defaultParams['include_url_preface']);
        }else{
            if($char === ''){

                // , or (
                $citation .= $this->apaAddIfElseCharacter([
                    $pub['volume'],
                    $pub['page_number'],
                ], ',', ' (');

                // Vol. volume_number
                $citation .= $this->apaAddIfElseCharacter([
                    $pub['volume'],
                ], '', 'Vol. ' . $pub['volume']);

                // , OR ''
                if(($pub['volume'] !== '' && $pub['volume'] != false) && ($pub['page_number'] !== '' && $pub['page_number'] != false)) $citation .= ', ';

                // pp. page_numbers
                $citation .= $this->apaGetPageNumbers($pub['page_number'], false);

                // '' or ).
                $citation .= $this->apaAddIfElseCharacter([
                    $pub['volume'],
                    $pub['page_number'],
                ], '', ').');

                // Pub City, PS: Publisher.
                $citation .= $this->apaGetPublishing($pub['pub_city_state'], $pub['publisher']);
            }
        }

        return $citation;
    }

    // Book Editor, Scholarly
    // Book Introduction
    // Book Preface
    // Book, Chapter in Non-Scholarly Book-New
    // Book, Chapter in Non-Scholarly Book-Revised
    // Book, Chapter in Scholarly Book-New
    // Book, Chapter in Scholarly Book-Revised
    // Book, Chapter in Textbook-New
    // Book, Chapter-Reprint
    // Book, Non-Scholarly-New
    // Book, Non-Scholarly-Revised
    // Book, Reprint in Translation
    // Book, Scholarly-New
    // Book, Scholarly-Revised
    // Book, Textbook-New
    // Book, Textbook-Revised
    // Online Open Source Textbook
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    /* Basic Book Format */
    ////// Lastname, F.M. (year). \i Book Title. Pub City, PS: Publisher
    //
    /* Online Library version or Database retrieval */
    ////// Lastname, F.M. (year). \i Book Title. Retrieved from website_url
    //
    /* Edition other than first */
    ////// Lastname, F.M. (year). \i Book Title (edition_number ed.). Pub City, PS: Publisher
    //
    /* Section of Book */
    ////// Lastname, F.M. (year). Section Title. In EditorLastname, F.M. & EditorLastname, F.M. (Eds.), \i Book Title (pp. page_numbers). Pub City, PS: Publisher
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatBookChapter($pub, $params = []){
        $defaultParams = array_merge(['include_url_preface' => true], $params);
        // If there are no authors, show editors
        // if(count($pub['authors']) < 1) $citation .= $this->apaGetEditors($pub['editors']);
        if(count($pub['authors']) < 1){
            $citation = $this->apaGetEditors($pub['editors']). ' ';
        }else{
            // Get Start Title
            $citation = $this->apaStructureAuthors($pub['authors']);
        }

        // (year).
        $citation .= $this->apaGetYear($pub['year_published']);

        // Check if chapter or piece of book, not entire work
        if(preg_match('/(chapter.*|review.*|preface.*|introduction.*|entry.*)/i', $pub['type'])){
            // Section Title.
            $st = $this->apaGetSecondaryTitle($pub['title_secondary']);
            $citation .= ($st !== '' ? $st . '.' : '');

            // In EditorLastname, F.M. & EditorLastname, F.M. (Eds.),
            $citation .= (count($pub['editors']) > 0 ? ' In ' . $this->apaGetEditors($pub['editors']) . ',' : '');
        }

        // If item is complete Book, or a chapter
        // Book Title
        $citation .= ' ' . $this->apaGetTitle($pub['title']);

        // (edition_number ed.)
        $citation .= $this->apaGetEdition($pub['issue']);

        // (pp. page_numbers). || .
        if(preg_match('/(chapter.*|review.*|preface.*|introduction.*|entry.*)/i', $pub['type'])){
            $citation .= $this->apaGetPageNumbers($pub['page_number'], true);
        }

        // . (in all of the formats)
        $citation .= '.';

        if($pub['url'] !== '' && $pub['url'] != false){
            // Retrieved from website_url
            $citation .= $this->apaGetUrl($pub['url'], $defaultParams['include_url_preface']);
        }else{
            // Pub City, PS: Publisher.
            $citation .= $this->apaGetPublishing($pub['pub_city_state'], $pub['publisher']);
        }

        return $citation;
    }

    // Co-Editor, Conference Proceedings
    // Conference Abstract
    // Conference Demonstration Paper
    // Conference Extended Abstract
    // Conference Non-Archival Papers
    // Conference Paper
    // Conference Position Paper
    // Conference Poster
    // Conference Proceeding
    // Conference Proceedings
    // Conference Published Video
    // Conference Short Paper
    // Conference Workshop Paper
    // International Workshop Proceedings
    // Invited paper for an academic workshop
    // Workshop Paper
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    /* Retrieved from Website */
    ////// Lastname, F.M. (year). \i Conference Title. Retrieved from website_url
    //
    /* Citing a live presentation, or print work */
    ////// Lastname, F.M. (year). \i Conference Title. media_type presented at Conference Name, Conf City, CS.
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatConferencePaper($pub, $params = []){
        $defaultParams = array_merge(['include_url_preface' => true], $params);
        // Get Start Title
        $citation = $this->apaStructureAuthors($pub['authors']);

        // (year).
        $citation .= $this->apaGetYear($pub['year_published']);

        // Conference Title.
        $citation .= ' ' . $this->apaGetTitle($pub['title']) . '.';

        if($pub['url'] !== '' && $pub['url'] != false){
            // Retrieved from website_url
            $citation .= $this->apaGetUrl($pub['url'], $defaultParams['include_url_preface']);
        }else{
            // media_type
            $citation .= ' ' . $this->apaGetType($pub['type'], false);

            // presented at Conference Name
            $citation .= $this->apaAddIfElseCharacter([
                $pub['title_secondary']
            ], '', ' presented at ' . $this->apaGetSecondaryTitle($pub['title_secondary']));

            // , or ''
            $citation .= $this->apaAddIfElseCharacter([
                $pub['conf_city_state']
            ], '', ', ' . trim($pub['conf_city_state']));

            // .
            $citation .= '.';
        }

        return $citation;
    }

    // Digital Project
    // Online Research Highlight
    // Web Gallery Introduction
    // Website Document
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    /* Publisher of document known */
    ////// Lastname, F.M. (year, Month). \i Document Name. Retrieved from name_or_institute_of_website: website_url
    //
    /* Publisher unknown */
    ////// name_or_institute_of_website. (year, Month day). \i Document Name. Retrieved from website_url
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatWebProject($pub, $params = []){
        $defaultParams = array_merge(['include_url_preface' => false], $params);
        // Get Start Title
        $citation = $this->apaStructureAuthors($pub['authors']);

        // Publisher known?
        $pubUnknown = count($pub['authors']) < 1;

        // If no authors listed, name_or_institute_of_website.
        if($pubUnknown) $citation .= $this->apaGetSecondaryTitle($pub['title_secondary']) . '.';

        // NOTE: no month or day data right now
        // (year).
        $citation .= $this->apaGetYear($pub['year_published']);

        // Document Name.
        $citation .= ' ' . $this->apaGetTitle($pub['title']) . '.';

        // Retrieved from
        $citation .= $this->apaAddIfElseCharacter([
            $pub['url'],
            $pub['title_secondary']
        ], '', ' Retrieved from');

        // name_or_institute_of_website:
        if(!$pubUnknown){
            $citation .= $this->apaAddIfElseCharacter([
                $pub['title_secondary']
            ], '', ' ' . $pub['title_secondary'] . ':');
        }

        // website_url
        $citation .= $this->apaGetUrl($pub['url'], $defaultParams['include_url_preface']);

        return $citation;
    }

    // Dissertation
    // Honors Thesis
    // Master's Thesis
    // Ph.D. Thesis
    // PhD Dissertation
    // thesis
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    /* Published */
    ////// Lastname, F.M. (year). \i Thesis or Dissertation Name (type). Available from database_or_institute. (AAT aat_number)
    //
    /* Unpublished */
    ////// Lastname, F.M. (year). \i Thesis or Dissertation Name (Unpublished type). database_or_institute, City, ST.
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatDissertationOrThesis($pub){
        // Get Start Title
        $citation = $this->apaStructureAuthors($pub['authors']);

        // (year).
        $citation .= $this->apaGetYear($pub['year_published']);

        // Thesis or Dissertation Name
        $citation .= ' ' . $this->apaGetTitle($pub['title']);

        // (type). OR (Unpublished type).
        $type = $this->apaGetType($pub['type'], false);
        if($pub['status'] === 'Published'){
            // (type).
            $citation .= ($type !== '.' ? ' (' : '') . $type . ($type !== '.' ? ').' : '');
        }else{
            // (Unpublished type).
            $citation .= ($type !== '.' ? ' (Unpublished ' : '') . $type . ($type !== '.' ? ').' : '');
        }

        // Available from
        $citation .= (($pub['status'] === 'Published' && ($pub['publisher'] !== '' && $pub['publisher'] != false)) ? ' Available from' : '');

        // database_or_institute.
        // NOTE: Right now ends in a period, but will have to change this if we ever get AAT number, or separate Pub City, PS
        $citation .= ($pub['publisher'] !== '' && $pub['publisher'] != false ? ' ' . $pub['publisher'] . '.' : '');

        // City, ST.
        // NOTE: This information is often in the database_or_institute field

        // (AAT aat_number)
        // NOTE: We do not store that number right now

        return $citation;
    }

    // Column
    // Editor of special issue of professional journal
    // Guest Editor for Special Issue of Journal
    // Journal Article, Academic Journal
    // Journal Article, In-House Journal
    // Journal Article, Professional Journal
    // Journal Article, Public or Trade Journal
    // Magazine/Trade Publication
    // Newspaper
    // Online Essay
    // Online publication
    // Panel
    // Regular Column in Journal or Newspaper
    // Special Issue
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    /* If Magazine... */
    //////  Lastname, F.M. (year, Month). ...
    //
    /* If Newspaper... */
    //////  Lastname, F.M. (year, Month day). ...
    //
    /* Journal and Magazine Print, or Journal Database */
    ////// Lastname, F.M. (year). Article Title. \i Journal/Magazine/Newspaper Title, volume_number(issue_number), page_numbers. doi:doi_number
    //
    /* Journal Web */
    ////// Lastname, F.M. (year). Article Title. \i Journal Title. Retrieved from website_url
    //
    /* Magazine Database */
    ////// Lastname, F.M. (year, Month). Article Title. \i Magazine Title, volume_number(issue_number), page_numbers. doi:doi_number. Retrieved from website_url
    //
    /* Newspaper Print */
    ////// Lastname, F.M. (year, Month day). Article Title. \i Newspaper Title, pp. page_numbers.
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatJournalEntry($pub, $params = []){
        $defaultParams = array_merge(['include_url_preface' => true], $params);
        // Get Start Title
        $citation = $this->apaStructureAuthors($pub['authors']);

        // NOTE: Do not have data for Month or day yet
        // (year).
        $citation .= $this->apaGetYear($pub['year_published']);

        // Article Title.
        $citation .= $this->apaGetSecondaryTitle($pub['title']) . '. ';

        // Journal/Magazine/Newspaper Title
        $citation .= $this->apaGetTitle($pub['publication']);

        // , or .
        $char = $this->apaAddIfElseCharacter([
            $pub['volume'],
            $pub['issue'],
            $pub['page_number'],
        ], '.', ',');

        // If this is a journal with a URL
        if(preg_match('/.*journal.*/i', $pub['type']) && ($pub['url'] !== '' && $pub['url'] != false)){
            // Retrieved from website_url
            $citation .= '.' . $this->apaGetUrl($pub['url'], $defaultParams['include_url_preface']);
        }else{
            $citation .= $char;

            // volume_number(issue_number)
            $citation .= $this->apaGetVolumeAndIssue($pub['volume'], $pub['issue'], '');

            // , or .
            if($pub['volume'] !== '' && $pub['volume'] != false) $citation .= $this->apaAddIfElseCharacter([
                $pub['page_number'],
            ], '.', ', ');

            // pp. page_numbers OR page_numbers
            if(preg_match('/.*newspaper.*/i', $pub['type'])){
                $citation .= $this->apaGetPageNumbers($pub['page_number'], false);
            }else{
                $citation .= $pub['page_number'];
            }

            // .
            $citation .= $this->apaAddIfElseCharacter([
                $pub['page_number'],
            ], '', '.');

            // doi:doi_number.
            // NOTE: no way to include DOI number as of now

            // .
            $citation .= $this->apaAddIfElseCharacter([
                $pub['url'],
            ], '', '.');

            // Retrieved from website_url
            $citation .= $this->apaGetUrl($pub['url'], $defaultParams['include_url_preface']);
        }

        return $citation;
    }

    // Editorial
    // Newsletter
    // Op-Ed
    // Opinion Piece
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    /* If Url included */
    ////// Lastname, F.M. (year, Month day). Editorial Title [type].  \i Publication Title. Retrieved from website_url
    //
    /* If Volume, Page Numbers, and Issue included */
    ////// Lastname, F.M. (year, Month day). Editorial Title [type]. \i Publication Title, volume_number(issue_number), pp. page_numbers
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatEditorial($pub, $params = []){
        $defaultParams = array_merge(['include_url_preface' => true], $params);
        // Get Start Title
        $citation = $this->apaStructureAuthors($pub['authors']);

        // NOTE: No data for Month or Day yet
        // (year).
        $citation .= $this->apaGetYear($pub['year_published']);

        // Editorial title
        $citation .= $this->apaGetSecondaryTitle($pub['title']);

        // [type].
        $citation .= $this->apaGetType($pub['type']);

        // Publication Title
        if($pub['publication'] !== '' && $pub['publication'] != false) $citation .= ' ' . $this->apaGetTitle($pub['publication']);

        // , or .
        $char = $this->apaAddIfElseCharacter([
            $pub['volume'],
            $pub['issue'],
            $pub['page_number'],
            $pub['pub_city_state'],
            $pub['publisher']
        ], '.', ',');
        if($pub['url'] !== '' && $pub['url'] != false){
            $citation .= '.';
        }else{
            $citation .= $char;
        }

        if($pub['url'] !== '' && $pub['url'] != false){
            // Retrieved from website_url
            $citation .= $this->apaGetUrl($pub['url'], $defaultParams['include_url_preface']);
        }else{
            if($char === ','){
                // volume_number(issue_number),
                $citation .= $this->apaGetVolumeAndIssue($pub['volume'], $pub['issue'], '');

                // , or .
                $citation .= $this->apaAddIfElseCharacter([
                    $pub['page_number'],
                    $pub['pub_city_state'],
                    $pub['publisher']
                ], '.', ', ');

                // pp. page_numbers
                $citation .= $this->apaGetPageNumbers($pub['page_number'], false);

                // , or .
                $citation .= $this->apaAddIfElseCharacter([
                    $pub['pub_city_state'],
                    $pub['publisher']
                ], '.', ', ');

                // Pub City, PS: Publisher.
                $citation .= $this->apaGetPublishing($pub['pub_city_state'], $pub['publisher']);
            }
        }

        return $citation;
    }

    // Research Brief
    // Research Protocol and Guidebook
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    /* If Publisher and City State included */
    ////// Lastname, F.M. (year). Article title. Pub City, PS: Publisher.
    //
    /* If Volume, Page Numbers, and Issue included */
    ////// Lastname, F.M.. (year, Month). Article title. \i Magazine Title, volume_number(issue_number), pp. page_numbers
    //
    /* If Url included */
    ////// Lastname, F.M. (year, Month Day). Article title. Retrieved from website_url
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatResearchBrief($pub, $params = []){
        $defaultParams = array_merge(['include_url_preface' => true], $params);
        // Get Start Title
        $citation = $this->apaStructureAuthors($pub['authors']);

        // NOTE: No data for Month or Day yet
        // (year).
        $citation .= $this->apaGetYear($pub['year_published']);

        // Article title.
        $citation .= $this->apaGetSecondaryTitle($pub['title']) . '.';

        // Magazine Title,
        if($pub['title_secondary'] !== '' && $pub['title_secondary'] != false) $citation .= $this->apaGetTitle($pub['title_secondary']) . ',';

        // volume_number(issue_number),
        $citation .= $this->apaGetVolumeAndIssue($pub['volume'], $pub['issue'], ',');

        // pp. page_numbers
        $citation .= $this->apaGetPageNumbers($pub['page_number'], false);

        if($pub['url'] !== '' && $pub['url'] != false){
            // Retrieved from website_url
            $citation .= $this->apaGetUrl($pub['url'], $defaultParams['include_url_preface']);
        }else{
            // Pub City, PS: Publisher.
            $citation .= $this->apaGetPublishing($pub['pub_city_state'], $pub['publisher']);
        }

        return $citation;
    }

    // Online video series
    // Computer-based training course
    // Video
    // Broadcast Media
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    ////// Insitution Name as Author. (year, Month day). \i Video title [type]. Platform. website_url
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatVideoSeries($pub, $params = []){
        $defaultParams = array_merge(['include_url_preface' => false], $params);
        // Get Start Title
        $citation = $this->apaStructureAuthors($pub['authors']);

        // NOTE: no data for month or day yet
        // (year).
        $citation .= $this->apaGetYear($pub['year_published']);

        // Video title
        $citation .= ' ' . $this->apaGetTitle($pub['title']);

        // [type].
        $citation .= $this->apaGetType($pub['type']);

        // Platform.
        $st = $this->apaGetSecondaryTitle($pub['title_secondary']);
        $citation .= ($st !== '' ? $st . '.' : '');

        // website_url
        $citation .= $this->apaGetUrl($pub['url'], $defaultParams['include_url_preface']);

        return $citation;
    }

    // A set of instruments to measure changes in one's capability changes as a result of ICT use (based on Capability Approach)
    // Abstract of poster presented at the annual meeting...
    // Art Catalog
    // Article for film screening of A Wrinkle in Time
    // Article for film screening of Fantastic Beasts: Th...
    // Creative Writing
    // Curriculum
    // Exhibition
    // Focus Group Field Research Guide
    // Guide
    // Instructor's Manual
    // Interview
    // Metadata Application Profile
    // Metadata Application Profile - Core Set
    // Metadata Application Profile - Recommended Set
    // Metadata Standard
    // Methods toolkit
    // Photo Exhibition
    // Preface to new edition
    // Published Keynote Address
    // Reference Library
    // Response to NSF "Dear Colleague Letter"
    // Study
    // Toolkit
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    /* If web*/
    ////// Lastname, F.M. (year). \i Custom Title (type). Retrieved from website_url
    //
    /* If print */
    ////// Lastname, F.M. (year). \i Custom Title (type). volume_number(issue_number), pp. page_numbers. PubCity, PS: author or Publisher.
    //
    /* If unpublished */
    ////// ... \i Custom Title (Unpublished type) ...
    //
    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function apaFormatDefaultItem($pub, $params = []){
        $defaultParams = array_merge(['include_url_preface' => true], $params);
        // Get Start Title
        $citation = $this->apaStructureAuthors($pub['authors']);

        // & Editors...,
        if(count($pub['editors']) > 0) $citation .= '& ' . $this->apaGetEditors($pub['editors']);

        // NOTE: Only have year for now
        // (year)
        $citation .= $this->apaGetYear($pub['year_published']);

        // Custom Title
        $citation .= ' ' . $this->apaGetTitle($pub['title']);

        // Only one of these should ever output. If both output, someone entered something wrong.
        if($pub['status'] !== 'Published'){
            // (Unpublished type)
            $citation .= ' (Unpublished ' . trim($pub['type']) . ').';
        }else{
            // (type).
            $citation .= ' (' . trim($pub['type']) . ').';
        }

        // volume_number(issue_number),
        $citation .= $this->apaGetVolumeAndIssue($pub['volume'], $pub['issue']);

        $character = $this->apaAddIfElseCharacter([
            $pub['volume'],
            $pub['issue']
        ], '', '.');

        // , or $character
        if($pub['url'] !== '' && $pub['url'] != false){
            if($character !== ''){
                $citation .= $this->apaAddIfElseCharacter([
                    $pub['page_number'],
                ], $character, ',');
            }
        }else{
            if($character !== ''){
                $citation .= $this->apaAddIfElseCharacter([
                    $pub['page_number'],
                    $pub['pub_city_state'],
                    $pub['publisher']
                ], $character, ',');
            }
        }

        // pp. page_numbers
        $citation .= $this->apaGetPageNumbers($pub['page_number'], false);

        if($pub['url'] !== '' && $pub['url'] != false){
            // Retrieved from website_url
            $citation .= $this->apaGetUrl($pub['url'], $defaultParams['include_url_preface']);
        }else{

            // . or ''
            if($character !== '' && ($pub['page_number'] !== '' ||$pub['page_number'] != false)) {
                $citation .= $this->apaAddIfElseCharacter([
                    $pub['pub_city_state'],
                    $pub['publisher'],
                ], '', '.');
            }

            // Pub City, PS: Publisher.
            $citation .= $this->apaGetPublishing($pub['pub_city_state'], $pub['publisher']);
        }

        return $citation;
    }

    /////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////
    // Formatting Functions
    ///////////////////////////////////////////////////////
    //////////////////////////////////////////////////////

	public function apaStructureAuthors($authors){
		// Formatted according to following source
		// https://owl.purdue.edu/owl/research_and_citation/apa_style/apa_formatting_and_style_guide/reference_list_author_authors.html
		// If there is no last name or first name (Editors(s) of a larger work), we will need to do a special format.

		// Reset indexes to avoid loop errors
		$authors = array_values($authors);

		// If there is only one author
		if(count($authors) === 1){
			return ($authors[0]['last_name'] !== '' ? trim($authors[0]['last_name']) . ($authors[0]['first_name'] !== '' ? ', ' : ' ') : '') . ($authors[0]['first_name'] !== '' ? ($authors[0]['last_name'] !== '' ? substr(trim($authors[0]['first_name']), 0, 1) . '.' : trim($authors[0]['first_name'])) . ' ' : '');
		}

		$citation = '';

		// If there is more than one author but less than 21
		if(count($authors) < 21){
			foreach($authors as $k => $author){
				$citation .= ($k == (count($authors) - 1) && $k !== 0 ? '& ' : '' ) . ($author['last_name'] !== '' ? trim($author['last_name']) . ($k !== (count($authors) - 1) ? ', ' : (($author['first_name'] !== '' || $author['middle_name'] !== '') ? ', ' : ' ')) : '') . ($author['first_name'] !== '' ? ($author['last_name'] !== '' ? substr(trim($author['first_name']), 0, 1)  . '.' : trim($author['first_name'])) : '') . ($author['middle_name'] !== '' ? ($author['last_name'] !== '' ? '' : ' ') . substr(trim($author['middle_name']), 0, 1) . '.' : '') . ($k == (count($authors) - 1) && ($author['first_name'] !== '' || $author['middle_name'] !== '') ? ' ' : ( ($author['first_name'] !== '' || $author['middle_name'] !== '') ? ', ' : '') );
			}
			return $citation;
		}

		// If there are at least 21 authors
		for($i = 0; $i < 20; $i++){
			$citation .= ($authors[$i]['last_name'] !== '' ? trim($authors[$i]['last_name']) . ($i !== (count($authors) - 1) ? ', ' : (($authors[$i]['first_name'] !== '' || $authors[$i]['middle_name'] !== '') ? ', ' : ' ')) : '') . ($authors[$i]['first_name'] !== '' ? ($authors[$i]['last_name'] !== '' ? substr(trim($authors[$i]['first_name']), 0, 1) . '.' : trim($authors[$i]['first_name'])) : '') . ($authors[$i]['middle_name'] !== '' ? ($authors[$i]['last_name'] !== '' ? '' : ' ') . substr(trim($authors[$i]['middle_name']), 0, 1) . '.' : '') . (($authors[$i]['first_name'] !== '' || $authors[$i]['middle_name'] !== '') ? ', ' : '');

			// If this is the last loop
			if($i === 19){
				$j = count($authors) - 1;
				$citation .= '... ' . ($authors[$j]['last_name'] !== '' ? trim($authors[$j]['last_name']) . ($j !== (count($authors) - 1) ? ', ' : (($authors[$j]['first_name'] !== '' || $authors[$j]['middle_name'] !== '') ? ', ' : ' ')) : '') . ($authors[$j]['first_name'] !== '' ? ($authors[$j]['last_name'] !== '' ? substr(trim($authors[$j]['first_name']), 0, 1)  . '.' : trim($authors[$j]['first_name'])) : '') . ($authors[$j]['middle_name'] !== '' ? ($authors[$j]['last_name'] !== '' ? '' : ' ') . substr(trim($authors[$j]['middle_name']), 0, 1) . '.' : '') . (($authors[$j]['first_name'] !== '' || $authors[$j]['middle_name'] !== '') ? ' ' : '');
			}
		}

		return $citation;

	}

	// TODO: split odd character , in string for Editor(s) of a Larger Work
	public function mergeEditors($editorsPrime, $editorsLarger){
		$roughDraft = explode(',', $editorsLarger);
		$arr = $editorsPrime;

		// This was not formatted properly, we are going to have to list at the end of our list in whatever format was written prior
		if(count($roughDraft)%2 !== 0){

			// See if there was more than one author listed. This should account for formatting of companies to look well enough.
			$moreThanOne = count($roughDraft) > 2;

			array_push($arr, [
				'first_name' => '',
				'middle_name' => '',
				'last_name' => ($moreThanOne ? '(' : '') . trim($editorsLarger) . ($moreThanOne ? ')' : ''),
			]);

		// Formatted properly, we can merge with our array and loop by twos
		}else{
			for($i=0; $i<count($roughDraft); $i+=2){
				$first_name = str_replace('&', '', $roughDraft[($i + 1)]);
				$first_name = trim($first_name);
				array_push($arr, [
					'first_name' => $first_name,
					'middle_name' => '',
					'last_name' => trim($roughDraft[$i]),
				]);
			}

			// Now re-alphabetize
			usort($arr, function($a, $b){
				return $a['last_name'] <=> $b['last_name'];
			});
		}

		return $arr;
	}

    // This function will sort Publications prior to formatting for APA
	public function sortForAPAGuidelines($content){
		// Sort the authors of a piece
		foreach($content as $key=>$pub){
			$pub['authors'] = array_filter($pub['authors'], function($val){
				return !($val['last_name'] == '' && $val['first_name'] == '');
			});
			usort($pub['authors'], function($a, $b){
				return $a['last_name'] <=> $b['last_name'];
			});
			// And then back to the content key
			$content[$key] = $pub;
		}

		// Now we are going to sort the publications/research based on the first author in the array
		// Since it's possible that two authors may have the same last name, or the exact same set of authors, sorting is a little complex
		usort($content, function($a, $b){
			// Recursive sorting hack O_O XP
			return $this->nestedAuthorSort($a, $b, 0);
		});

		return $content;
	}

	// This function will sort Research Projects prior to custom formatting
	public function sortForResearchGuidelines($content){
		usort($content, function($a, $b){
			$a['sort_year'] = ($a['start_date'] != false && $a['start_date'] != '' ? $a['start_date'] : $a['end_date']);
			$b['sort_year'] = ($b['start_date'] != false && $b['start_date'] != '' ? $b['start_date'] : $b['end_date']);
			if($a['sort_year'] === $b['sort_year']){
				if($a['sort_year'] === $b['sort_year']){
					return strtolower($a['title']) <=> strtolower($b['title']);
				}
				return $b['sort_year'] <=> $a['sort_year'];
			}
			return $b['sort_year'] <=> $a['sort_year'];
		});

		return $content;
	}

	// This is a recursive hack sort function designed specifically for complex sorting on publications or research grants
	private function nestedAuthorSort($a, $b, $index = 0){
		// If the authors are exactly the same
		$a_authors = isset($a['authors']) ? $a['authors'] : $a['collaborators'];
		$b_authors = isset($b['authors']) ? $b['authors'] : $b['collaborators'];

		if($a_authors == $b_authors){
			return strtolower($a['title']) <=> strtolower($b['title']);
		}

		// Else, loop through authors until the data is different
		if($a_authors[$index] === $b_authors[$index]){
			$index += 1;
			return $this->nestedAuthorSort($a, $b, $index);
		}

		// And if somehow they have the same last name, sort by first name
		if(strtolower($a_authors[$index]['last_name']) === strtolower($b_authors[$index]['last_name'])){
			return strtolower($a_authors[$index]['first_name']) <=> strtoLower($b_authors[$index]['first_name']);
		}

		// Otherwise, sort by the standard last name
		return strtolower($a_authors[$index]['last_name']) <=> strtolower($b_authors[$index]['last_name']);
	}

	// Checks an array to see if items are falsey. If all items are falsey, it adds the char1. If at least on value is truthy, it adss $char2
	public function apaAddIfElseCharacter($arr, $char1, $char2 = ''){
		$none = true;
		foreach($arr as $item){
			if(trim($item) !== '' && $item !== null){
				$none = false;
				break;
			}
		}

		return ($none ? $char1 : $char2);
	}

	public function apaGetEdition($edition, $character = ''){

		// Adds appropriate ending after number; 1st, 2nd, 3rd..., or in some cases, nothing because the ai user already added the ending
		if(substr($edition, -1) == '1'){
			$ending = 'st';
		}elseif(substr($edition, -1) == '2'){
			$ending = 'nd';
		}elseif(substr($edition, -1) == '3'){
			$ending = 'rd';
		}elseif(is_numeric(substr($edition, -1))){
			$ending = 'th';
		}else{
			$ending = '';
		}

		return ($edition != null && $edition !== '' ? ' (' . $edition . $ending . ' ed.)' . $character : '');
	}

	public function apaGetEditors($editors){
		// Basically set up authors and then add an 'Ed' or 'Eds' after
		return ($this->apaStructureAuthors($editors) . (count($editors) > 1 || ( isset($editors[0]) && isset($editors[0]['last_name']) && strpos($editors[0]['last_name'], '&') !== null) ? '(Eds.)' : '(Ed.)'));
	}

	// Format for Pages
	public function apaGetPageNumbers($pages, $parantheses = false){
		if($pages !== '' && $pages != null){
			$format = (strpos($pages, '-') === null ? 'p. ' : 'pp. ') . trim($pages);
			if($parantheses){
				$format = '(' . $format . ')';
			}
			return ' ' . $format;
		}
		return '';
	}

	// Format for Publishing info
	public function apaGetPublishing($cityState, $publisher){
		$format = ($cityState !== '' && $cityState != null ? ' ' . trim($cityState) . ':' : '');
		$format .= ($publisher !== '' && $publisher != null ? ' ' . trim($publisher) . '.' : '');

		return $format;
	}

	// Format a review or chapter title
	public function apaGetSecondaryTitle($title){
		return ($title !== '' && $title != null ? ' ' . trim($title) : '');
	}

	// Format for title
	public function apaGetTitle($title){
		return '<em>' . trim($title) . '</em>';
	}

	// Format for types
	public function apaGetType($type, $includeBrackets = true){
		return ($type !== '' && $type != null ? ($includeBrackets ? ' [' : '') . trim($type) . ($includeBrackets ? '].' : '') : '.');
	}

	// Format the most common url
	public function apaGetUrl($url, $includePreface = true){
		return ($url !== '' && $url != null ? ($includePreface ? ' Retrieved from ' : ' ') . '<a href="' . $url . '" target="_blank">' . $url . '</a>' : '');
	}

	// Format volume and issue numbers
	public function apaGetVolumeAndIssue($volume, $issue, $character = ''){
		return (($volume != null && $volume !== '' ? ' ' . $volume : ($issue != null && $issue !== '' ? 'Unknown Volume' : '')) . ($issue != null && $issue !== '' ? '(' . $issue . ')' . $character : ($volume != null && $volume !== '' ? '(Unknown Issue)' . $character : '' )));
	}

	// Format the year
	public function apaGetYear($year){
		return ('(' . ($year !== '' && null != $year ? trim($year) : 'Unknown Year' ) .').');
	}
}
