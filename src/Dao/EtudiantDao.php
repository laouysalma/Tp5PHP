<?php
declare(strict_types=1);

namespace App\Dao;

use PDO; 
use PDOException;
use App\Entity\Etudiant;
use App\Database\DBConnection;
use App\Log\Logger;

class EtudiantDao
{
    /** logger pour garder les erreurs */
    private $logger;

    // constructeur
    public function __construct(Logger $logger)
    {
        $this->logger = $logger; // on stocke le logger
    }

    // ajouter un étudiant
    public function insert(Etudiant $e): int
    {
        // requête d’insertion
        $sql = 'INSERT INTO etudiant(cne, nom, prenom, email, filiere_id) 
                VALUES(:cne, :nom, :prenom, :email, :filiere_id)';
        try {
            $pdo = DBConnection::get(); // connexion BD
            $stmt = $pdo->prepare($sql); // préparation

            // on lie les valeurs
            $stmt->bindValue(':cne', $e->getCne(), PDO::PARAM_STR);
            $stmt->bindValue(':nom', $e->getNom(), PDO::PARAM_STR);
            $stmt->bindValue(':prenom', $e->getPrenom(), PDO::PARAM_STR);
            $stmt->bindValue(':email', $e->getEmail(), PDO::PARAM_STR);
            $stmt->bindValue(':filiere_id', $e->getFiliereId(), PDO::PARAM_INT);

            $stmt->execute(); // exécution

            $id = (int)DBConnection::get()->lastInsertId(); // id généré
            $e->setId($id); // on met l’id dans l’objet

            return $id;

        } catch (PDOException $ex) {

            // si erreur → on log
            $this->logger->error($ex->getMessage(), [
                'method' => __METHOD__, 
                'sql' => $sql, 
                'cne' => $e->getCne(), 
                'email' => $e->getEmail()
            ]);

            throw $ex; // on relance
        }
    }

    // modifier étudiant
    public function update(Etudiant $e): bool
    {
        // requête update
        $sql = 'UPDATE etudiant 
                SET cne=:cne, nom=:nom, prenom=:prenom, email=:email, filiere_id=:filiere_id 
                WHERE id=:id';
        try {
            $pdo = DBConnection::get();
            $stmt = $pdo->prepare($sql);

            // récupérer les données
            $id = $e->getId(); 
            $cne = $e->getCne(); 
            $nom = $e->getNom(); 
            $prenom = $e->getPrenom(); 
            $email = $e->getEmail(); 
            $fid = $e->getFiliereId();

            // liaison paramètres
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':cne', $cne, PDO::PARAM_STR);
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':filiere_id', $fid, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->rowCount() > 0; // true si modifié

        } catch (PDOException $ex) {

            $this->logger->error($ex->getMessage(), [
                'method' => __METHOD__, 
                'sql' => $sql, 
                'id' => $e->getId()
            ]);

            throw $ex;
        }
    }

    // supprimer étudiant
    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM etudiant WHERE id = :id';
        try {
            $pdo = DBConnection::get();
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT); // id à supprimer
            $stmt->execute();

            return $stmt->rowCount() > 0; // true si supprimé

        } catch (PDOException $ex) {

            $this->logger->error($ex->getMessage(), [
                'method' => __METHOD__, 
                'sql' => $sql, 
                'id' => $id
            ]);

            throw $ex;
        }
    }

    // chercher par id
    public function findById(int $id): ?Etudiant
    {
        $sql = 'SELECT id, cne, nom, prenom, email, filiere_id 
                FROM etudiant WHERE id = :id';
        try {
            $pdo = DBConnection::get();
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch();

            if (!$row) { 
                return null; // rien trouvé
            }

            // créer objet avec données BD
            return new Etudiant(
                (int)$row['id'],
                (string)$row['cne'],
                (string)$row['nom'],
                (string)$row['prenom'],
                (string)$row['email'],
                (int)$row['filiere_id']
            );

        } catch (PDOException $ex) {

            $this->logger->error($ex->getMessage(), [
                'method' => __METHOD__, 
                'sql' => $sql, 
                'id' => $id
            ]);

            throw $ex;
        }
    }

    // récupérer tous les étudiants
    public function findAll(): array
    {
        $sql = 'SELECT id, cne, nom, prenom, email, filiere_id 
                FROM etudiant ORDER BY id ASC';
        try {
            $pdo = DBConnection::get();
            $stmt = $pdo->query($sql); // pas de param donc query

            $rows = $stmt->fetchAll();
            $out = [];

            // transformer chaque ligne en objet
            foreach ($rows as $r) {
                $out[] = new Etudiant(
                    (int)$r['id'],
                    (string)$r['cne'],
                    (string)$r['nom'],
                    (string)$r['prenom'],
                    (string)$r['email'],
                    (int)$r['filiere_id']
                );
            }

            return $out;

        } catch (PDOException $ex) {

            $this->logger->error($ex->getMessage(), [
                'method' => __METHOD__, 
                'sql' => $sql
            ]);

            throw $ex;
        }
    }
}