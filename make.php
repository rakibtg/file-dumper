<?php
  require_once 'vendor/autoload.php';

  class DataMaker {
    protected $faker;
    protected $file;
    protected $expectedFileSize;
    protected $expectedFileSizeUnit;
    protected $totalBytesWritten;

    function __construct ($file, $fileSize, $fileSizeUnit) {
      $this->file = $file;
      $this->faker = Faker\Factory::create();
      $this->expectedFileSize = $fileSize;
      $this->expectedFileSizeUnit = $fileSizeUnit;
      $this->totalBytesWritten = 0;
    }
    function row () {
      $row = [
        'name' => $this->faker->name,
        'email' => $this->faker->email,
        'age' => $this->faker->numberBetween(13, 60),
        'streetName' => $this->faker->streetName,
        'streetAddress' => $this->faker->streetAddress,
        'postcode' => $this->faker->postcode,
        'country' => $this->faker->country,
        'state' => $this->faker->state,
        'city' => $this->faker->city,
        'company' => $this->faker->company,
        'jobTitle' => $this->faker->jobTitle,
        'timezone' => $this->faker->timezone,
        'website' => $this->faker->domainName,
        'languageCode' => $this->faker->languageCode,
        'currencyCode' => $this->faker->currencyCode,
        'emoji' => $this->faker->emoji,
        'about' => $this->faker->realText(250, 2),
      ];
      return json_encode($row);
    }
    function fileSize ($bytes, $decimals = 0) {
      // $bytes = filesize($this->file);
      $size = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
      $factor = floor((strlen($bytes) - 1) / 3);
      $sizeAmount = sprintf("%.{$decimals}f", $bytes / pow(1024, $factor));
      $sizeUnit = preg_replace("/[^A-Z]+/", "", (sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor]));
      echo $sizeAmount . $sizeUnit . "\n";
      if ($sizeAmount <= $this->expectedFileSize) {
        if ($sizeUnit == $this->expectedFileSizeUnit) {
          return false;
        } else {
          return true;
        }
      } else {
        return true;
      }
    }
    function make () {
      $fileHandler = fopen($this->file, 'rw+');
      while (true) {
        $row = $this->row() . PHP_EOL;
        $totalBytesWritten += strlen($row);
        $this->fileSize($this->totalBytesWritten);
        fwrite($fileHandler, $row);
      }
      fclose($fileHandler);
    }
  }

  $dm = new DataMaker('./db.sdb', '1', 'MB');
  $dm->make();

  echo "\n";