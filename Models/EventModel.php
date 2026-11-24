<?php
declare(strict_types=1);

class EventModel
{
    private int $id = 0;
    private string $titre = '';
    private string $description = '';
    private string $categorie = '';
    private string $dateEvent = '';
    private string $lieu = '';
    private int $capaciteMax = 50;
    private string $statut = 'actif';
    private int $createdBy = 0;
    private int $nbInscrits = 0;
    private bool $isRegistered = false;

    public function getId(): int { return $this->id; }
    public function getTitre(): string { return $this->titre; }
    public function getDescription(): string { return $this->description; }
    public function getCategorie(): string { return $this->categorie; }
    public function getDateEvent(): string { return $this->dateEvent; }
    public function getLieu(): string { return $this->lieu; }
    public function getCapaciteMax(): int { return $this->capaciteMax; }
    public function getStatut(): string { return $this->statut; }
    public function getCreatedBy(): int { return $this->createdBy; }
    public function getNbInscrits(): int { return $this->nbInscrits; }
    public function isRegistered(): bool { return $this->isRegistered; }
    public function isActif(): bool { return $this->statut === 'actif'; }

    public function setId(int $id): void { $this->id = $id; }
    public function setTitre(string $titre): void { $this->titre = $titre; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setCategorie(string $categorie): void { $this->categorie = $categorie; }
    public function setDateEvent(string $dateEvent): void { $this->dateEvent = $dateEvent; }
    public function setLieu(string $lieu): void { $this->lieu = $lieu; }
    public function setCapaciteMax(int $capaciteMax): void { $this->capaciteMax = $capaciteMax; }
    public function setStatut(string $statut): void { $this->statut = $statut; }
    public function setCreatedBy(int $createdBy): void { $this->createdBy = $createdBy; }
    public function setNbInscrits(int $nbInscrits): void { $this->nbInscrits = $nbInscrits; }
    public function setIsRegistered(bool $isRegistered): void { $this->isRegistered = $isRegistered; }

    public static function fromArray(array $row): self
    {
        $event = new self();
        $event->setId((int) ($row['id'] ?? 0));
        $event->setTitre((string) ($row['titre'] ?? ''));
        $event->setDescription((string) ($row['description'] ?? ''));
        $event->setCategorie((string) ($row['categorie'] ?? ''));
        $event->setDateEvent((string) ($row['date_event'] ?? ''));
        $event->setLieu((string) ($row['lieu'] ?? ''));
        $event->setCapaciteMax((int) ($row['capacite_max'] ?? 50));
        $event->setStatut((string) ($row['statut'] ?? 'actif'));
        $event->setCreatedBy((int) ($row['created_by'] ?? 0));
        $event->setNbInscrits((int) ($row['nb_inscrits'] ?? 0));
        $event->setIsRegistered((bool) ($row['is_registered'] ?? false));
        return $event;
    }
}
