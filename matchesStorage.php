<?php

class MatchesStorage extends Storage {
  public function __construct() {
    parent::__construct(new JsonIO('matches.json'));
  }
}