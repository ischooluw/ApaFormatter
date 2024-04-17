# APA Formatter Trait

This package is for formatting publications and research grants into APA formatted html. This package has prebuilt functions to format your publication, or individual functions to pass to create an APA format with more customality.

## List of Primary Functions

- apaFormatBookChapter()
- apaFormatBookEntry()
- apaFormatBookReview()
- apaFormatConferencePaper()
- apaFormatDissertationOrThesis()
- apaFormatEditorial()
- apaFormatForeword()
- apaFormatJournalEntry()
- apaFormatReport()
- apaFormatSoftware()
- apaFormatVideoGame()
- apaFormatVideoSeries()
- apaFormatWebProject()
- apaFormatResearchBrief()
- apaFormatDefaultItem()

To Keep this README manageable, please look in trait code for examples of what types of publications should be used with which function. The Code has very in depth comments about this.

## List of Secondary Functions
Sometimes we want to do special formatting (i.e. hyperlinking a book name) for our APA formatted entry. In that case, use the secondary building functions to create a custom entry.

- apaStructureAuthors() - Outputs author string from Array
- apaAddIfElseCharacter() - Very specialized Shorthad logic. Sometimes we need a comma, sometimes we don't. We can pass values here to check that all exist before we output that character.
- apaGetEdition() - Formats Edition of publication
- apaGetEditors() - Outputs editors string from Array
- apaGetPageNumbers() - Formats page numbers of publication
- apaGetPublishing() - Sets up Publiching info from the city/state and publisher passed
- apaGetSecondaryTitle() - Outputs a chapter entry or journal name if secondary title is present
- apaGetTitle() - Get Title as emphasized html for publication
- apaGetType() - Formats type of publication if present
- apaGetUrl() - Formats an anchor tag with url if present
- apaGetVolumeAndIssue() - Formats Volume and Issue appropriately if none, some, or both are present
- apaGetYear() - Returns year or properly formats that year is unknown

There are many examples in the Primary Functions' code that illustrate how these may be used in conjunction

## Example Publication Array

A Publication is an array passed. Not every publication will have the following properties, but this is a complete list of probably properties for a publication array:

```php
        $pub = [
            'authors' => [
                [
                    'last_name' => 'Isaka',
                    'first_name' => 'Kotaro',
                    'middle_name' => ''
                ],
                [
                    'last_name' => 'Hayashi',
                    'first_name' => 'Tamio',
                    'middle_name' => ''
                ]
            ],
            'conf_city_state' => 'New York, NY',
            'editors' => [
                [
                    'last_name' => 'Oohata',
                    'first_name' => 'Eisuke',
                    'middle_name' => ''
                ]
            ],
            'issue' => 6,
            'page_number' => '320',
            'pub_city_state' => 'New York, NY',
            'publication' => 'Fisshū Sutōrī',
            'publisher' => 'New York Asian Film Festival',
            'status' => 'Published',
            'title' => 'Fish Story',
            'title_secondary' => 'The Story of My Life if My Life Were a Fish',
            'type' => 'Movie',
            'url' => 'https://en.wikipedia.org/wiki/Fish_Story_(film)',
            'volume' => 1,
            'year_published' => 2009,
```

Note: Unfortunately, with our limited information, this trait does not support fields for a Publisher Platform or DOI:DOI Number.
