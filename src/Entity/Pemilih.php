<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PemilihRepository")
 */
class Pemilih
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nama;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $alamat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $jenisKelamin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $kelurahan;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $kecamatan;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $kota;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $provinsi;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nik;

    public function __construct()
    {
        $this->id = Uuid::Uuid4();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNama(): ?string
    {
        return $this->name;
    }

    public function setNama(string $nama): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAlamat(): ?string
    {
        return $this->alamat;
    }

    public function setAlamat(string $alamat): self
    {
        $this->alamat = $alamat;

        return $this;
    }

    public function getJenisKelamin(): ?string
    {
        return $this->jenisKelamin;
    }

    public function setJenisKelamin(string $jenisKelamin): self
    {
        $this->jenisKelamin = $jenisKelamin;

        return $this;
    }

    public function getKelurahan(): ?string
    {
        return $this->kelurahan;
    }

    public function setKelurahan(string $kelurahan): self
    {
        $this->kelurahan = $kelurahan;

        return $this;
    }

    public function getKecamatan(): ?string
    {
        return $this->kecamatan;
    }

    public function setKecamatan(string $kecamatan): self
    {
        $this->kecamatan = $kecamatan;

        return $this;
    }

    public function getKota(): ?string
    {
        return $this->kota;
    }

    public function setKota(string $kota): self
    {
        $this->kota = $kota;

        return $this;
    }

    public function getProvinsi(): ?string
    {
        return $this->provinsi;
    }

    public function setProvinsi(string $provinsi): self
    {
        $this->provinsi = $provinsi;

        return $this;
    }

    public function getNik(): ?string
    {
        return $this->nik;
    }

    public function setNik(string $nik): self
    {
        $this->nik = $nik;

        return $this;
    }
}
