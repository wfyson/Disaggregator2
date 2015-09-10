<?php

$conf = iotaConf::getInstance();

class DisaggregatorPerson extends adro implements tauUser
{

	public static function getUserById($username)
	{
		if(strlen($username = strtolower($username)) < 2) throw new tauUserInvalidUserException("No username was specified");
			
		$model = DisaggregatorModel::get();
                
		$query = new ADROQuery($model);
		
		$users = $query->addTable($model->getTable('person'))->addRestriction(new adroQueryEq($query, 'person.Username', $username))->run();
		
		if($users->count() < 1) throw new tauUserInvalidUserException("No user was found with the given username");
			
		return $users->get(0);
	}

	public function getUserID()
	{

	}

	public function hash($string)
        {
        	return sha1($this->Username.$string);
        }

	public function authenticate($secret)
	{
                $conf = iotaConf::getInstance();

		$pw = $this->Password;
		
		if($this->hash($secret) == $pw)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function getdocuments($reverse = false, $security = false)
	{
		$model = DisaggregatorModel::get();
		$query = new ADROQuery($model);
                $query->addTable($model->getTable('document'));
		$query->addRestriction(new adroQueryEq($query, 'document.UserID', $this->UserID));
                if($security)
                    $query->addRestriction(new adroQueryEq($query, 'document.Security', $security));
		if(!$reverse)
                    $query->setOrder('document.DocumentID DESC');
		return $query->run();	
	}
        
        public function getContributor()
        {
            $model = DisaggregatorModel::get();
            $query = new ADROQuery($model);
            $query->addTable($model->getTable('contributor'));
            $query->addRestriction(new adroQueryEq($query, 'contributor.UserID', $this->UserID));
            $contributors = $query->run();
            return $contributors->get(0);
        }
}

class disaggregatorUtil
{

    public static function getCurrentUser()
    {
        $file = tauFile::getCurrentFile();

        try
        {
            return $file->getDecoration('user');
        }
        catch(iotaException $e)
        {
            return false;
        }
    }
    
    public static function requireUser()
    {
        $file = tauFile::getCurrentFile();

        try
        {
            $user = $file->getDecoration('user');
        }
        catch(iotaException $e)
        {
            debug_print_backtrace();
        }

        // Users that aren't logged in should be shown the login page
        if($user instanceof TauDummyUser)
        {
                $errfile = new tauFile(iotaStore::getInstance()->get(iotaConf::getInstance()->get('tauUser_errorLoginRequired')));
                $file->renchain->clear(); // Abort current chain
                $file->setContents($errfile->render()); // Replace with error
                return false;
        }

        return $user;
    }
}
