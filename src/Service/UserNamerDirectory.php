<?php
	/**
	 * Created by PhpStorm.
	 * User: FonsecaGay
	 * Date: 01/04/2019
	 * Time: 16:08
	 */
	
	namespace App\Service;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
	use Vich\UploaderBundle\Mapping\PropertyMapping;
	
	class UserNamerDirectory extends Controller implements DirectoryNamerInterface
	{
		public function directoryName($object, PropertyMapping $mapping): string
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();
			return '_files_'.$user->getUsername().'/';
		}
	}