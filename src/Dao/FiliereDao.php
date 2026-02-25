<?php
declare(strict_types=1);

namespace App\Dao;

use PDO; 
use PDOException;
use App\Entity\Filiere;
use App\Database\DBConnection;
use App\Log\Logger;

class FiliereDao
{
    /** @var Logger logger erreurs */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger; // init logger
    }

    // ajouter filiere
    public function insert(Filiere $f): int
    {
        $sql = 'INSERT INTO filiere(code, libelle) VALUES(:code, :libelle)';
        try {
            $pdo = DBConnection::get(); // connexion
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':code', $f->getCode(), PDO::PARAM_STR); // bind code
            $stmt->bindValue(':libelle', $f->getLibelle(), PDO::PARAM_STR); // bind libelle

            $stmt->execute(); // execute
            $id = (int)$pdo->lastInsertId(); // recup id
            $f->setId($id);

            return $id;

        } catch (PDOException $e) {
            $this->logger->error($e->getMessage(), ['method' => __METHOD__, 'sql' => $sql]);
            throw $e; // relance erreur
        }
    }

    // modifier filiere
    public function update(Filiere $f): bool
    {
        $sql = 'UPDATE filiere SET code = :code, libelle = :libelle WHERE id = :id';
        try {
            $pdo = DBConnection::get();
            $stmt = $pdo->prepare($sql);

            $id = $f->getId(); 
            $code = $f->getCode(); 
            $lib = $f->getLibelle();

            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // bind id
            $stmt->bindParam(':code', $code, PDO::PARAM_STR); // bind code
            $stmt->bindParam(':libelle', $lib, PDO::PARAM_STR); // bind libelle

            $stmt->execute();
            return $stmt->rowCount() > 0; // check update

        } catch (PDOException $e) {
            $this->logger->error($e->getMessage(), [
                'method' => __METHOD__, 
                'sql' => $sql, 
                'id' => $f->getId()
            ]);
            throw $e;
        }
    }

    // supprimer filiere
    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM filiere WHERE id = :id';
        try {
            $pdo = DBConnection::get();
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT); // bind id
            $stmt->execute();

            return $stmt->rowCount() > 0; // check delete

        } catch (PDOException $e) {
            $this->logger->error($e->getMessage(), [
                'method' => __METHOD__, 
                'sql' => $sql, 
                'id' => $id
            ]);
            throw $e;
        }
    }

    // chercher par id
    public function findById(int $id): ?Filiere
    {
        $sql = 'SELECT id, code, libelle FROM filiere WHERE id = :id';
        try {
            $pdo = DBConnection::get();
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch();
            if (!$row) { return null; } // pas trouvé

            return new Filiere(
                (int)$row['id'], 
                (string)$row['code'], 
                (string)$row['libelle']
            );

        } catch (PDOException $e) {
            $this->logger->error($e->getMessage(), [
                'method' => __METHOD__, 
                'sql' => $sql, 
                'id' => $id
            ]);
            throw $e;
        }
    }

    // lister tout
    public function findAll(): array
    {
        $sql = 'SELECT id, code, libelle FROM filiere ORDER BY id ASC';
        try {
            $pdo = DBConnection::get();
            $stmt = $pdo->query($sql);

            $rows = $stmt->fetchAll();
            $out = [];

            foreach ($rows as $r) {
                $out[] = new Filiere(
                    (int)$r['id'], 
                    (string)$r['code'], 
                    (string)$r['libelle']
                );
            }

            return $out;

        } catch (PDOException $e) {
            $this->logger->error($e->getMessage(), [
                'method' => __METHOD__, 
                'sql' => $sql
            ]);
            throw $e;
        }
    }
}