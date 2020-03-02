<?php

class Welsh_Tribunal {

  private $data = [];
  private $delete = [];
  private $keys = [];

  public function __construct()
  {

    $this->setRecordKeys();

    $this->readJsonFiles();

    $this->sortRecords();

    $this->formatResponse();

  }


  /**
   * 
   */
  private function readJsonFiles()
  {
    $files = glob('data/*.{json}', GLOB_BRACE);
    foreach($files as $file) {
      $this->loadLocation($file);
    }
  }


  /**
   * 
   */
  private function loadLocation($place)
  {
    $json = file_get_contents($place);

    $data = json_decode($json,true);

    $this->formatData($data,$place);
  }


  /**
   * 
   */
  private function formatData($data,$place)
  {
    foreach ($data['resources'] as $key => $value) {

      $idArr = explode('/',$value['on']);

      $handle = $idArr[5];

      $recordArr = explode('<p>',$value['resource']['chars']);

      $this->data[] = [
        'place'   => str_replace(['data/','.json'],'',$place),
        'handle'  => 'https://viewer.library.wales/'.$handle,
        'data'    => $recordArr
      ];
    }

  }


  /**
   * 
   */
  private function sortRecords()
  {

    // loop all the records
    foreach($this->data as $key => $val) {

      // if the tag for this record contains page 1
      if (
        strpos(end($val['data']), 'Page 1') !== false || 
        strpos(end($val['data']), 'page 1') !== false) {

        // loop through the next record and add that data to 
        // the record of page 1

        // check if the next record contains page 2
        if (
          strpos(end($this->data[$key+1]['data']), 'Page 2') !== false || 
          strpos(end($this->data[$key+1]['data']), 'page 2') !== false) {

          foreach($this->data[$key+1]['data'] as $k => $v) {
            $this->data[$key]['data'][] = $v;
          }
        
          // // delete page 2
          $this->delete[] = $key+1;
        }
      }
    }
  }

  /**
   * 
   */
  private function formatResponse()
  {
    // remove page 2 from data array
    foreach($this->delete as $k => $v) {
      unset($this->data[$v]);
    }

    $formatted_records = [];

    // loop all the records
    foreach($this->data as $k => $v) {

      // make copy of the keys we want
      $loop_record = $this->keys;

      // add handle URL
      $loop_record['URL'] = $v['handle'];
      $loop_record['Document'] = $v['place'];

      // loop through all the records and add
      // them to out formatted array
      foreach($v['data'] as $i => $j) {
        $parts = explode(':',$j);
        
        $loopKey = $parts[0];

        $loop_record[$loopKey] .= str_replace($loopKey .':', '', $j);

      }

      $formatted_records[] = $loop_record;
    }

    $this->writeCsv($formatted_records);
   
  }


  /**
   * 
   */
  private function writeCsv($records) 
  {

    $keys = $this->keys;

    $keysArr = [];
    foreach($keys as $k => $v) {
      $keysArr[] = $k;
    }

    $list[] = $keysArr;

    foreach($records as $key => $val) {
      
      $tempArr = [];

      foreach($val as $k => $v) {
        $tempArr[] = $v;
      }

      $list[] = $tempArr;
    }

    $fp = fopen('file.csv', 'w');

    foreach ($list as $fields) {
    fputcsv($fp, $fields);
    }

    fclose($fp);

  }

  /**
   * 
   */
  private function setRecordKeys()
  {

    $this->keys = [
      'Document' => '',
      'Name of Local Tribunal' => '',
      'Number of Case' => '',
      'Name' => '',
      'Address' => '',
      'Occupation, profession or business' => '',
      'Attested or not attested' => '',
      'Grounds' => '',
      'Signature of appellant' => '',
      'Address of appellant' => '',
      'Why appellant acts for the man' => '',
      'Date' => '',
      'Tag' => '',
      'Reason for decision of Local Tribunal' => '',
      'Decision' => '',
      'Description' => '',
      'Transcription' => '',
      'Name of Tribunal' => '',
      'Age' => '',
      'Where attested' => '',
      'Number of group' => '',
      'Name of employer' => '',
      'Business' => '',
      'Nature of application' => '',
      'Reasons in support of the application' => '',
      'Signature' => '',
      'Decision of Tribunal' => '',
      'Name of present employer' => '',
      'Employer address' => '',
      'Reasons' => '',
      'Date of application' => '',
      'Decision of the Tribunal' => '',
      'Number on group card' => '',
      'Date of birth' => '',
      'Married or single' => '',
      'Previous applications' => '',
      'Date of marriage' => '',
      'Address at date of National Registration' => '',
      'Date of attestation' => '',
      'Precise occupation before 15 August 1915' => '',
      'Length of occupation before 15 August 1915' => '',
      'Name and address of last employer before 15 August 1915' => '',
      'Whether any previous application has been made' => '',
      'Why application made by applicant for the man' => '',
      'Date of last examination' => '',
      'Address of place of employment' => '',
      'Reasons in support of application' => '',
      'Signature of the man' => '',
      'Medical grade category' => '',
      'Voluntarily attested' => '',
      'Address on National Registration Certificate' => '',
      'Continued …' => '',
      'Change of conditions or circumstances' => '',
      'Signature of the applicant' => '',
      'Regional number' => '',
      'URL' => ''
    ];

  }
}

(new Welsh_Tribunal());