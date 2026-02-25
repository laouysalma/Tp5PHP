<?php

declare(strict_types = 1);

namespace App\Entity;

class Etudiant
{
private $id;        // id auto
private $cne;       // CNE unique
private $nom;       // nom  etudiant
private $prenom;    // prenom etudiant
private $email;     // email  etudiant unique
private $filiereId; // int FK -> filiere.id

public function __construct(?int $id, string $cne, string $nom, string $prenom, string $email, int $filiereId)
{
$this->id = $id;
$this->setCne($cne);
$this->setNom($nom);
$this->setPrenom($prenom);
$this->setEmail($email);
$this->setFiliereId($filiereId);
}

public function getId(): ?int { return $this->id;
}
public function setId(?int $id): void { $this->id = $id;
} // definir id

public function getCne(): string { return $this->cne;
} // lire CNE
public function setCne(string $cne): void
{
$cne = trim($cne);
if ($cne === '') { throw new \InvalidArgumentException('cne requis');
}
$this->cne = $cne;
}// set  CNE

public function getNom(): string { return $this->nom;
}// lire nom 
public function setNom(string $nom): void
{
$nom = trim($nom);
if ($nom === '') { throw new \InvalidArgumentException('nom requis');
}
$this->nom = $nom;
}//set nom etudiant 

public function getPrenom(): string { return $this->prenom;
}// lire  prenom etudiant 

public function setPrenom(string $prenom): void
{
$prenom = trim($prenom);
if ($prenom === '') { throw new \InvalidArgumentException('prenom requis');
}
$this->prenom = $prenom;
} // set prenom etudiant 

public function getEmail(): string { return $this->email;
}
public function setEmail(string $email): void
{
$email = trim($email);
if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
throw new \InvalidArgumentException('email invalide');
}
$this->email = $email;
}

public function getFiliereId(): int { return $this->filiereId;
}
public function setFiliereId(int $filiereId): void
{
if ($filiereId <= 0) { throw new \InvalidArgumentException('filiere_id > 0 requis');
}
$this->filiereId = $filiereId;
}
}  
