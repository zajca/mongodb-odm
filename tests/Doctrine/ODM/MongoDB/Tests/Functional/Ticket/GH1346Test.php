<?php

namespace Doctrine\ODM\MongoDB\Tests\Functional\Ticket;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

class GH1346Test extends \Doctrine\ODM\MongoDB\Tests\BaseTest
{
    public function testPublicDistanceProperty()
    {
        $coordinates = new GH1346Coordinates();
        $coordinates->setLatitude(10);
        $coordinates->setLongitude(10);

        $refrenced1 = new GH1346ReferencedDocument();
        $refrenced1->setCoordinates($coordinates);

        $refrenced2 = new GH1346ReferencedDocument();
        $refrenced2->setCoordinates($coordinates);

        $gH1346Document = new GH1346Document();

        $this->dm->persist($refrenced2);
        $this->dm->persist($refrenced1);
        $this->dm->persist($gH1346Document);
        $this->dm->flush();

        $gH1346Document->addReference($refrenced1);

        $this->dm->persist($gH1346Document);
        $this->dm->flush();

        $gH1346Document->addReference($refrenced2);

        $this->dm->persist($gH1346Document);
        $this->dm->flush();

        $this->assertEquals(2, $gH1346Document->getReferences()->count());

        $this->dm->remove($gH1346Document);
        $this->dm->remove($refrenced2);
        $this->dm->remove($refrenced2);
        $this->dm->flush();
        $this->dm->clear();
    }
}


/**
 * @ODM\Document
 */
class GH1346Document
{
    /** @ODM\Id */
    protected $id;

    /** @ODM\ReferenceMany(targetDocument="GH1346ReferencedDocument") */
    protected $references;

    public function __construct()
    {
        $this->references = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getReferences()
    {
        return $this->references;
    }

    public function setReferences($references)
    {
        $this->references = $references;
    }

    public function hasReference($reference)
    {
        return $this->references->contains($reference);
    }

    public function addReference($reference)
    {
        $this->references->add($reference);
    }
}


/**
 * @ODM\Document(collection="city")
 * @ODM\Index(keys={"coordinates"="2d"})
 * @ODM\InheritanceType("COLLECTION_PER_CLASS")
 */
class GH1346ReferencedDocument
{
    /** @ODM\Distance */
    public $distance;
    /** @ODM\Id */
    protected $id;
    /** @ODM\EmbedOne(targetDocument="Coordinates") */
    protected $coordinates;

    public function getCoordinates()
    {
        return $this->coordinates;
    }

    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;
    }

    public function getDistance()
    {
        return $this->distance;
    }

    public function setDistance($distance)
    {
        $this->distance = $distance;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}


/** @ODM\EmbeddedDocument */
class GH1346Coordinates
{
    /** @ODM\Float */
    public $latitude;

    /** @ODM\Float */
    public $longitude;

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }
}
