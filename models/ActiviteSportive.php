<?php
// models/ActiviteSportive.php

class ActiviteSportive {
    private $id;
    private $nom;
    private $description;
    private $categorie;

    public function __construct($nom = null, $description = null, $categorie = null, $id = null) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->categorie = $categorie;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNom() { return $this->nom; }
    public function setNom($nom) { $this->nom = $nom; }

    public function getDescription() { return $this->description; }
    public function setDescription($description) { $this->description = $description; }

    public function getCategorie() { return $this->categorie; }
    public function setCategorie($categorie) { $this->categorie = $categorie; }
}
