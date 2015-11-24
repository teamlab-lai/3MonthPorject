<?php

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Adapter\Memory as AclList;

/**
 * SecurityPlugin
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
class SecurityPlugin extends Plugin
{
	/**
	 * Returns an existing or new access control list
	 *
	 * @returns AclList
	 */
	public function getAcl()
	{

		//throw new \Exception("something");

		//if (!isset($this->persistent->acl)) {

			$acl = new AclList();

			$acl->setDefaultAction(Acl::DENY);

			//Register roles
			$roles = array(
				'users'  => new Role('matomeUsers'),
				'guests' => new Role('matomeGuests')
			);
			foreach ($roles as $role) {
				$acl->addRole($role);
			}

			//Private area resources
			$privateResources = array(
				'favorite'	 => array('index','create','delete'),
				'management' => array('index'),
				'post'		 => array('index','create','delete','doDelete'),
				'comment'	 => array('index','picture','video','text','createUrl','createPicture','createVideo','createText','delete','doDelete'),
				'topic'		 => array('oyaMatome','koMatome'),
				'location'	 => array('index'),
				'FbMethod'	 => array('likeDelete','likeCreate','postComment'),
			);
			foreach ($privateResources as $resource => $actions) {
				$acl->addResource(new Resource($resource), $actions);
			}

			//Public area resources
			$publicResources = array(
				'index'      => array('index'),
				'errors'     => array('show401', 'show404', 'show500'),
				'session'    => array('index', 'register', 'login', 'logout','testLogin'),
				'search'     => array('index','doSearch','history','deleteHistory'),
				'reload'	 => array('index'),
				'back'	 	 => array('index'),
				'topic'		 => array('index'),


			);
			foreach ($publicResources as $resource => $actions) {
				$acl->addResource(new Resource($resource), $actions);
			}

			//Grant access to public areas to both users and guests
			foreach ($roles as $role) {
				foreach ($publicResources as $resource => $actions) {
					foreach ($actions as $action){
						$acl->allow($role->getName(), $resource, $action);
					}
				}
			}

			//Grant acess to private area to role Users
			foreach ($privateResources as $resource => $actions) {
				foreach ($actions as $action){
					$acl->allow('matomeUsers', $resource, $action);
				}
			}

			//The acl is stored in session, APC would be useful here too
			$this->persistent->acl = $acl;
		//}

		return $this->persistent->acl;
	}

	/**
	 * This action is executed before execute any action in the application
	 *
	 * @param Event $event
	 * @param Dispatcher $dispatcher
	 */
	public function beforeDispatch(Event $event, Dispatcher $dispatcher)
	{

		$auth = $this->session->get('matome_auth');
		if (!$auth){
			$role = 'matomeGuests';
		} else {
			$role = 'matomeUsers';
		}

		$controller = $dispatcher->getControllerName();
		$action = $dispatcher->getActionName();
		$acl = $this->getAcl();
		$allowed = $acl->isAllowed($role, $controller, $action);
		if ($allowed != Acl::ALLOW) {
			$dispatcher->forward(array(
				'controller' => 'session',
				'action'     => 'index'
				/*
				'controller' => 'errors',
				'action'     => 'show401'
				*/
			));
            //$this->session->destroy();
			return false;
		}
	}

}
