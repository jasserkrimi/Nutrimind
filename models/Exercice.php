<?php
// models/Exercice.php

class Exercice {
    private $id;
    private $nom;
    private $description;
    private $url_video;
    private $difficulte;
    private $activite_id;

    public function __construct($nom = null, $description = null, $url_video = null, $difficulte = null, $activite_id = null, $id = null) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->url_video = $url_video;
        $this->difficulte = $difficulte;
        $this->activite_id = $activite_id;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNom() { return $this->nom; }
    public function setNom($nom) { $this->nom = $nom; }

    public function getDescription() { return $this->description; }
    public function setDescription($description) { $this->description = $description; }

    public function getUrlVideo() { return $this->url_video; }
    public function setUrlVideo($url_video) { $this->url_video = $url_video; }

    public function getDifficulte() { return $this->difficulte; }
    public function setDifficulte($difficulte) { $this->difficulte = $difficulte; }

    public function getActiviteId() { return $this->activite_id; }
    public function setActiviteId($activite_id) { $this->activite_id = $activite_id; }
}
