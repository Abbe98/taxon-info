<?php
class Taxon {
  public $wikidata;
  public $settings;

  private $wdEndpoint = 'https://www.wikidata.org/w/api.php?action=wbgetentities&format=json&ids=';
  private $commonsEndpoint = 'https://commons.wikimedia.org/w/api.php?action=query&format=json&prop=pageimages&piprop=thumbnail&pithumbsize=400&titles=File:';
  private $taxon;

  public function __construct($wikidata, $decodedSettings) {
    $this->wikidata = $wikidata;
    $this->settings = $decodedSettings;

    $query = $this->wdEndpoint . $wikidata;
    $this->taxon = json_decode(file_get_contents($query, false, NULL));
  }

  public function getTaxon() {
    $data = $this->taxon->{'entities'}->{$this->wikidata};

    $result = new stdClass();
    $result->title = $data->labels->en->value;

    $image = @$data->claims->P18[0]->mainsnak->datavalue->value;
    $rawImage = @$this->getRawImage($image);
    $result->image = isset($image) ? $image : false;
    $result->rawImage = isset($rawImage) ? $rawImage : false;

    $commons = @$data->claims->P373[0]->mainsnak->datavalue->value;
    $result->commons =  isset($commons) ? $commons : false;

    $result->topLanguages = Array();
    foreach ($this->settings->languages as $language) {
      // #TODO provide a way to create a article if it does not exists
      if (isset($data->sitelinks->{$language . 'wiki'})) {
        $article = new stdClass();
        $article->title = $data->sitelinks->{$language . 'wiki'}->title;
        $article->link = 'https://' . $language . '.wikipedia.org/wiki/' . $article->title;
        $article->language = $language;
        $result->topLanguages[] = $article;
      }
    }

    return $result;
  }

  private function getRawImage($filename) {
    $query = $this->commonsEndpoint . urlencode($filename);
    $data = json_decode(file_get_contents($query, false, NULL));

    foreach ($data->query->pages as $page) {
      return $page->thumbnail->source;
    }
  }

  // $settingsFile = name of settings file without extension
  // has to run from index.php
  public static function loadSettings($settingsFile) {
    return json_decode(file_get_contents('settings/' . $settingsFile . '.json'));
  }
}