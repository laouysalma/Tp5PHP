<?php
declare(strict_types=1);

// Autoload minimal (si pas de composer)
spl_autoload_register(function(string $class){
    $prefix = 'App\\';
    $base = __DIR__ . '/src/';
    $len = strlen($prefix);
    if (strncmp($class, $prefix, $len) !== 0) return;
    $rel = substr($class, $len);
    $file = $base . str_replace('\\', DIRECTORY_SEPARATOR, $rel) . '.php';
    if (is_file($file)) require $file;
});

use App\Log\Logger;
use App\Database\DBConnection;
use App\Dao\FiliereDao;
use App\Dao\EtudiantDao;
use App\Entity\Filiere;
use App\Entity\Etudiant;

// Logger pour les erreurs PDO
$logger = new Logger(__DIR__ . '/Tp5PHP/logs/pdo_errors.log');
DBConnection::init($logger);

$filiereDao = new FiliereDao($logger);
$etudiantDao = new EtudiantDao($logger);

// Fonction simple pour afficher les résultats
function out(string $label, $value): void {
    echo "[INFO] $label : " . (is_scalar($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE)) . PHP_EOL;
}

// ----------------- CRUD Filiere -----------------
// Ajouter une filière informatique
$f = new Filiere(null, 'INF', 'Informatique générale');
$idF = $filiereDao->insert($f);
out('Filière ajoutée', $idF);

// Vérification
$foundF = $filiereDao->findById($idF);
out('Filière trouvée', $foundF ? $foundF->getCode() . ' - ' . $foundF->getLibelle() : 'non trouvée');

// Liste complète
$allF = $filiereDao->findAll();
out('Nombre total filières', count($allF));

// Mise à jour
$f->setLibelle('Informatique avancée');
out('Mise à jour filière', $filiereDao->update($f));

// Suppression
out('Suppression filière', $filiereDao->delete($idF));

// ----------------- CRUD Etudiant -----------------
$fInfo = $filiereDao->findById(1);
if (!$fInfo) {
    $tmp = new Filiere(null, 'INFO', 'Informatique générale');
    $filiereDao->insert($tmp);
    $fInfo = $tmp;
}

// Ajouter un étudiant
$e = new Etudiant(null, 'CNE101', 'Asma', 'Salma', 'asma.salma@example.com', (int)$fInfo->getId());
$idE = $etudiantDao->insert($e);
out('Etudiant ajouté', $idE);

// Vérifier l’étudiant
$foundE = $etudiantDao->findById($idE);
out('Etudiant trouvé', $foundE ? $foundE->getNom() . ' ' . $foundE->getPrenom() : 'non trouvé');

// Liste complète
$allE = $etudiantDao->findAll();
out('Nombre total étudiants', count($allE));

// Mise à jour nom
$e->setNom('Asma-Update');
out('Mise à jour étudiant', $etudiantDao->update($e));

// Suppression
out('Suppression étudiant', $etudiantDao->delete($idE));

// ----------------- Test email dupliqué -----------------
try {
    $dup1 = new Etudiant(null, 'CNE102', 'Salma', 'Hind', 'salma.hind@example.com', (int)$fInfo->getId());
    $etudiantDao->insert($dup1);

    $dup2 = new Etudiant(null, 'CNE103', 'Fati', 'Hind', 'salma.hind@example.com', (int)$fInfo->getId());
    $etudiantDao->insert($dup2);

    out('Test email dupliqué', 'Erreur attendue mais pas détectée');
} catch (\PDOException $ex) {
    out('Test email dupliqué', 'Exception détectée comme prévu');
}

// ----------------- Transaction -----------------
$pdo = DBConnection::get();
try {
    $pdo->beginTransaction();

    $fT = new Filiere(null, 'BD', 'Big Data');
    $filiereDao->insert($fT);

    $eT = new Etudiant(null, 'CNE104', 'Hind', 'Fati', 'hind.fati@example.com', (int)$fT->getId());
    $etudiantDao->insert($eT);

    $pdo->commit();
    out('Transaction', 'Transaction terminée avec succès');
} catch (\PDOException $ex) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    out('Transaction', 'Transaction annulée : ' . $ex->getMessage());
}
