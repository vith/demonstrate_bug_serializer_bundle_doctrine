<?php

namespace AbcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Util\Debug;
use Symfony\Component\HttpFoundation\Response;
use AbcBundle\Entity\Thing;

class DefaultController extends Controller
{
    /**
     * @Route()
     */
    public function indexAction()
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();

        $this->header("jms_serializer.object_constructor:");
        $this->dump($this->get('jms_serializer.object_constructor'));

        // create a new entity
        $this->header('thing:');
        $thing = new Thing('hello');
        $em->persist($thing);
        $em->flush();
        $this->dump($thing);

        // serialize
        $this->header("serialize to json:");
        $thingJson = $serializer->serialize($thing, 'json');
        $this->dump($thingJson);

        // deserialize
        $this->header("deserialize from json:");
        $deserializedThing = $serializer->deserialize($thingJson, 'AbcBundle\Entity\Thing', 'json');
        $this->dump($deserializedThing);

        // check
        $this->header("check that the entity is managed:");
        $entityState = $em->getUnitOfWork()->getEntityState($deserializedThing);
        $isPersisted = (\Doctrine\ORM\UnitOfWork::STATE_MANAGED === $entityState) ? 'persisted' : 'not persisted';
        $this->dump($isPersisted);

        return new Response;
    }

    private function dump($value)
    {
        echo "<pre>";
        Debug::dump($value);
        echo "</pre>" . PHP_EOL . PHP_EOL;
    }

    private function header($text)
    {
        echo "<h1>$text</h1>" . PHP_EOL . PHP_EOL;
    }
}
