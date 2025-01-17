<?php

namespace Geissler\Converter\Standard\CSL;

use Geissler\Converter\Interfaces\CreatorInterface;
use Geissler\Converter\Model\Entries;
use Geissler\Converter\Model\Persons;
use Geissler\Converter\Model\Dates;

/**
 * Create csl input data.
 *
 * @author  Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Creator implements CreatorInterface
{
    /** @var array */
    private $data = [];

    /**
     * Create entries based on the given standard from the \Geissler\Converter\Model\Entries object.
     *
     * @param \Geissler\Converter\Model\Entries $data
     * @return boolean
     */
    public function create(Entries $data)
    {
        if (count($data) > 0) {
            foreach ($data as $entry) {
                /** @var \Geissler\Converter\Model\Entry $entry */
                $record =   [];

                $record['type'] =   $this->getType($entry->getType()->getType());

                $persons        =   [
                    'author'                =>  'getAuthor',
                    'collection-editor'     =>  'getCollectionEditor',
                    'container-author'      =>  'getContainerAuthor',
                    'director'              =>  'getDirector',
                    'editor'                =>  'getEditor',
                    'editorial-director'    =>  'getEditorialDirector',
                    'illustrator'           =>  'getIllustrator',
                    'interviewer'           =>  'getInterviewer',
                    'original-author'       =>  'getOriginalAuthor',
                    'recipient'             =>  'getRecipient',
                    'reviewed-author'       =>  'getReviewedAuthor',
                    'translator'            =>  'getTranslator',
                ];
                foreach ($persons as $field => $getter) {
                    $person =   $this->createPerson($entry->$getter());

                    if (count($person) > 0) {
                        $record[$field] =   $person;
                    }
                }

                $dates  =   [
                    'accessed'      =>  'getAccessed',
                    'event-date'    =>  'getEventDate',
                    'issued'        =>  'getIssued',
                    'original-date' =>  'getOriginalDate',
                    'submitted'     =>  'getSubmitted',
                ];
                foreach ($dates as $field => $getter) {
                    $date   =   $this->createDate($entry->$getter());

                    if (count($date) > 0) {
                        $record[$field] =   $date;
                    }
                }

                // pages
                if ($entry->getPages()->getRange() !== null) {
                    $record['page'] =   $entry->getPages()->getRange();
                } elseif ($entry->getPages()->getStart() !== null && $entry->getPages()->getEnd() !== null) {
                    $record['page'] =   $entry->getPages()->getStart() . '-' . $entry->getPages()->getEnd();
                } elseif ($entry->getPages()->getStart() !== null) {
                    $record['page'] =   $entry->getPages()->getStart();
                } elseif ($entry->getPages()->getEnd() !== null) {
                    $record['page'] =   $entry->getPages()->getEnd();
                } elseif ($entry->getPages()->getTotal() !== null) {
                    $record['page'] =   $entry->getPages()->getTotal();
                }

                if ($entry->getPages()->getStart() !== null) {
                    $record['page-first'] =   $entry->getPages()->getStart();
                }

                $fields = [
                    'abstract'                    => 'getAbstract',
                    'annote'                      => 'getAnnote',
                    'archive'                     => 'getArchive',
                    'archive_location'            => 'getArchiveLocation',
                    'archive-place'               => 'getArchivePlace',
                    'authority'                   => 'getAuthority',
                    'call-number'                 => 'getCallNumber',
                    'citation-label'              => 'getCitationLabel',
                    'collection-title'            => 'getCollectionTitle',
                    'container-title'             => 'getContainerTitle',
                    'container-title-short'       => 'getContainerTitleShort',
                    'dimensions'                  => 'getDimensions',
                    'DOI'                         => 'getDOI',
                    'event'                       => 'getEvent',
                    'event-place'                 => 'getEventPlace',
                    'genre'                       => 'getGenre',
                    'ISBN'                        => 'getISBN',
                    'ISSN'                        => 'getISSN',
                    'jurisdiction'                => 'getJurisdiction',
                    'keyword'                     => 'getKeyword',
                    'medium'                      => 'getMedium',
                    'note'                        => 'getNote',
                    'original-publisher'          => 'getOriginalPublisher',
                    'original-publisher-place'    => 'getOriginalPublisherPlace',
                    'original-title'              => 'getOriginalTitle',
                    'PMCID'                       => 'getPMCID',
                    'PMID'                        => 'getPMID',
                    'publisher'                   => 'getPublisher',
                    'publisher-place'             => 'getPublisherPlace',
                    'references'                  => 'getReferences',
                    'reviewed-title'              => 'getReviewedTitle',
                    'scale'                       => 'getScale',
                    'section'                     => 'getSection',
                    'source'                      => 'getSource',
                    'status'                      => 'getStatus',
                    'title'                       => 'getTitle',
                    'title-short'                 => 'getTitleShort',
                    'URL'                         => 'getURL',
                    'version'                     => 'getVersion',
                    'yearSuffix'                  => 'getYearSuffix',
                ];

                foreach ($fields as $field => $getter) {
                    $value  =   $entry->$getter();
                    if (
                        $value != '' && $value !== null
                        && ((is_array($value) == true && count($value) > 0) || is_array($value) == false)
                    ) {
                        $record[$field] =   $value;
                    }
                }

                $this->data[]   =   $record;
            }

            return true;
        }

        return false;
    }

    /**
     * Retrieve the created standard data. Return false if standard could not be created.
     *
     * @return string|boolean
     */
    public function retrieve()
    {
        if (isset($this->data) == true && count($this->data) > 0) {
            return json_encode($this->data);
        }

        return false;
    }

    /**
     * Retrieve the entry type.
     *
     * @param string $type
     * @return string
     */
    private function getType($type)
    {
        switch ($type) {
            case 'articleJournal':
                return 'article-journal';
            case 'articleMagazine':
                return 'article-magazine';
            case 'articleNewspaper':
                return 'article-newspaper';
            case 'dictionary':
            case 'encyclopedia':
                return 'entry';
            case 'entryDictionary':
                return 'entry-dictionary';
            case 'entryEncyclopedia':
                return 'entry-encyclopedia';
            case 'legalCase':
                return 'legal_case';
            case 'motionPicture':
                return 'motion_picture';
            case 'musicalScore':
                return 'musical_score';
            case 'paperConference':
                return 'paper-conference';
            case 'postWeblog':
                return 'post-weblog';
            case 'personalCommunication':
                return 'personal_communication';
            case 'reviewBook':
                return 'review-book';
            default:
                return $type;
        }
    }

    /**
     * Convert \Geissler\Converter\Model\Person objects into csl names.
     *
     * @param \Geissler\Converter\Model\Persons $persons
     * @return array
     */
    private function createPerson(Persons $persons)
    {
        $data   =   [];
        $mapper =   [
            'family'                =>  'getFamily',
            'given'                 =>  'getGiven',
            'dropping-particle'     =>  'getDroppingParticle',
            'non-dropping-particle' =>  'getNonDroppingParticle',
            'suffix'                =>  'getSuffix',
        ];

        foreach ($persons as $person) {
            $entry  =   [];

            foreach ($mapper as $key => $getter) {
                if ($person->$getter() !== '') {
                    $entry[$key]    =   $person->$getter();
                }
            }

            $data[] =   $entry;
        }

        return $data;
    }

    /**
     * Convert \Geissler\Converter\Model\Date objects into csl dates.
     *
     * @param \Geissler\Converter\Model\Dates $dates
     * @return array
     */
    private function createDate(Dates $dates)
    {
        $data   =   [];

        foreach ($dates as $date) {
            /** @var \Geissler\Converter\Model\Date $date */
            $entry  =   [];
            if ($date->getYear() !== null) {
                $entry['year']  =   $date->getYear();
            }

            if ($date->getMonth() !== null) {
                $entry['month']  =   $date->getMonth();
            }

            if ($date->getDay() !== null) {
                $entry['day']  =   $date->getDay();
            }

            $data[] =   $entry;
        }

        return $data;
    }
}
