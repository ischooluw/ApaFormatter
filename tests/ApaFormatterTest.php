<?php

use Booellean\ApaFormatter\ApaFormatterTrait;

class ApaFormatterTest extends Orchestra\Testbench\TestCase
{
    public function testMethods(): void
    {
        // No Publication would have all these items, but we aren't going to set up a different publication for every item
        $pub = [
            'authors' => [
                [
                    'last_name' => 'Fakenamington',
                    'first_name' => 'Henry',
                    'middle_name' => ''
                ],
                [
                    'last_name' => 'Trefoil',
                    'first_name' => 'Threelaced',
                    'middle_name' => 'Thirds'
                ]
            ],
            'conf_city_state' => 'New York, NY',
            'editors' => [
                [
                    'last_name' => 'Branheart',
                    'first_name' => 'Corey',
                    'middle_name' => ''
                ]
            ],
            'issue' => 6,
            'page_number' => '67',
            'pub_city_state' => '',
            'publication' => 'The Newsiest News Story',
            'publisher' => 'Coming Around Publishing',
            'status' => 'Published',
            'title' => 'A Very Interesting Life',
            'title_secondary' => 'An exploration of what we are an what we\'ll be',
            'type' => 'Journal',
            'url' => 'https://en.wikipedia.org/wiki/Fish_Story_(film)',
            'volume' => 1,
            'year_published' => 2020,
        ];


        ////////////////////////////////////////////////////////////////
        // Primary Formatters
        ////////////////////////////////////////////////////////////////

        $mock = $this->getObjectForTrait(ApaFormatterTrait::class);

        $this->assertIsString($mock->apaFormatVideoGame($pub));
        $this->assertIsString($mock->apaFormatForeword($pub));
        $this->assertIsString($mock->apaFormatReport($pub));
        $this->assertIsString($mock->apaFormatSoftware($pub));
        $this->assertIsString($mock->apaFormatBookReview($pub));
        $this->assertIsString($mock->apaFormatBookEntry($pub));
        $this->assertIsString($mock->apaFormatBookChapter($pub));
        $this->assertIsString($mock->apaFormatConferencePaper($pub));
        $this->assertIsString($mock->apaFormatWebProject($pub));
        $this->assertIsString($mock->apaFormatDissertationOrThesis($pub));
        $this->assertIsString($mock->apaFormatJournalEntry($pub));
        $this->assertIsString($mock->apaFormatEditorial($pub));
        $this->assertIsString($mock->apaFormatResearchBrief($pub));
        $this->assertIsString($mock->apaFormatVideoSeries($pub));
        $this->assertIsString($mock->apaFormatDefaultItem($pub));

        ////////////////////////////////////////////////////////////////
        // Sorters
        ////////////////////////////////////////////////////////////////
        // TODO
        // mergeEditors
        // sortForResearchGuidelines
        // sortForAPAGuidelines
    }
}
