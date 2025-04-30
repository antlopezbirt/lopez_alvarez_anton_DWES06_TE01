<?php

namespace App\Models\DTOs;

use JsonSerializable;

class ItemDTO implements JsonSerializable {

    private $title;
    private $artist;
    private $format;
    private $year;
    private $origYear;
    private $label;
    private $rating;
    private $comment;
    private $buyPrice;
    private $condition;
    private $sellPrice;
    private $externalIds;

    /**
     * Constructor de la clase ItemDTO.
     *
     * @param string $title El título del ítem.
     * @param string $artist El artista del ítem.
     * @param string $format El formato del ítem (ej. "LP", "CD").
     * @param int $year El año de publicación del ítem.
     * @param int $origYear El año de publicación original del ítem.
     * @param string $label El sello discográfico.
     * @param int $rating La calificación del ítem (de 1 a 10).
     * @param string $comment Comentarios sobre el ítem.
     * @param float $buyPrice El precio de compra del ítem.
     * @param string $condition La condición del ítem (ej. "M", "NM").
     * @param float $sellPrice El precio de venta del ítem. Por defecto es NULL.
     * @param array $externalIds Identificadores externos asociados al ítem, por defecto un array vacío.
     * 
     */

    public function __construct(
        string $title, string $artist, string $format, int $year, 
        int $origYear, string $label, int $rating, string $comment, 
        float $buyPrice, string $condition, ?float $sellPrice = null,
        array $externalIds = []
    ) {

        $this->title = $title;
        $this->artist = $artist;
        $this->format = $format;
        $this->year = $year;
        $this->origYear = $origYear;
        $this->label = $label;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->buyPrice = $buyPrice;
        $this->condition = $condition;
        $this->sellPrice = $sellPrice;
        $this->externalIds = $externalIds;
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }


    /**
     * Get the value of title
     */
    public function getTitle()
    {
        return $this->title;
    }



    /**
     * Get the value of artist
     */
    public function getArtist()
    {
        return $this->artist;
    }



    /**
     * Get the value of format
     */
    public function getFormat()
    {
        return $this->format;
    }



    /**
     * Get the value of year
     */
    public function getYear()
    {
        return $this->year;
    }



    /**
     * Get the value of label
     */
    public function getLabel()
    {
        return $this->label;
    }


    /**
     * Get the value of rating
     */
    public function getRating()
    {
        return $this->rating;
    }



    /**
     * Get the value of comment
     */
    public function getComment()
    {
        return $this->comment;
    }



    /**
     * Get the value of buyPrice
     */
    public function getBuyPrice()
    {
        return $this->buyPrice;
    }


    /**
     * Get the value of condition
     */
    public function getCondition()
    {
        return $this->condition;
    }


    /**
     * Get the value of externalIds
     */
    public function getExternalIds()
    {
        return $this->externalIds;
    }


    /**
     * Get the value of origYear
     */
    public function getOrigYear()
    {
        return $this->origYear;
    }

    /**
     * Get the value of sellPrice
     */
    public function getSellPrice()
    {
        return $this->sellPrice;
    }

}