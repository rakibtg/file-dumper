<?php
  require_once 'vendor/autoload.php';

  class DataMaker {
    protected $faker;
    protected $file;
    protected $expectedFileSize;
    protected $expectedFileSizeUnit;
    protected $totalLinesWritten;
    protected $totalBytesWritten;

    function __construct ($file, $fileSize, $fileSizeUnit) {
      $this->file = $file;
      $this->faker = Faker\Factory::create();
      $this->expectedFileSize = $fileSize;
      $this->expectedFileSizeUnit = $fileSizeUnit;
      $this->totalBytesWritten = 0;
      $this->totalLinesWritten = 0;
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
    function formatSizeUnits ($bytes) {
      if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
      } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
      } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
      } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
      } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
      } else {
        $bytes = '0 bytes';
      }
      return $bytes;
    }
    function keepWriting () {
      [ $unitValue, $unitType ] = explode(" ", $this->formatSizeUnits($this->totalBytesWritten));
      $unitType = strtolower(trim($unitType));
      $unitValue = (int) $unitValue;
      if ($unitType === strtolower($this->expectedFileSizeUnit)) {
        if ($unitValue >= $this->expectedFileSize) {
          return false;
        }
      }
      return true;
    }
    function clearConsole () {
      echo chr(27).chr(91).'H'.chr(27).chr(91).'J';
    }
    function displayStats () {
      $this->clearConsole();
      echo "File is being updated\n";
      echo "Total Lines:\t" . $this->totalLinesWritten . "\n";
      echo "File Size:\t" . $this->formatSizeUnits($this->totalBytesWritten) . "\n";
    }
    function make () {
      $fileHandler = fopen($this->file, 'rw+');
      while ($this->keepWriting()) {
        $row = $this->row() . PHP_EOL;
        $this->totalBytesWritten += strlen($row);
        $this->totalLinesWritten += 1;
        fwrite($fileHandler, $row);
        $this->displayStats();
      }
      fclose($fileHandler);
      $this->displayStats();
    }
  }

  $dm = new DataMaker('./data-1gb.sdb', '1', 'gb');
  $dm->make();

  echo "\n";