<?php



class DisaggregatorModel extends ADROModel
{
	private static $instance = false;
    	public static function get($tables=null)
	{
            if(!self::$instance)
            {	
                self::$instance = new DisaggregatorModel($conn = self::genConn(), $tables);
                iotaConf::getInstance()->DisaggregatorDB = $conn;
            }

            return self::$instance;
	}


        public static function genConn()
        {
		$conf = iotaconf::getInstance();
                $host = $conf->get('dbhost');
                $user = $conf->get('dbuser');
                $pass = $conf->get('dbpass');
                $name = $conf->get('dbname');

                return new adroConn($host, $name, $user, $pass);
        }

	public function __construct(ADROConn $conn, $tables=array(), $configure=true)
	{           
	       	parent::__construct($conn, null, $tables);
            
            	if($configure)
	        {
         	       $this->configure();
	        }            
        	$this->conn = $conn;
        }


	protected function configure()
        {
            	$this->registerClass($this->person, 'DisaggregatorPerson');
		$this->registerClass($this->document, 'Document');
		$this->registerClass($this->descriptor, 'Descriptor');
                $this->registerClass($this->field, 'Field');
                $this->registerClass($this->descriptorfield, 'DescriptorField');
                $this->registerClass($this->contributor, 'Contributor');
                $this->registerClass($this->component, 'Component');                
                
                $this->registerClass($this->filevalue, 'FileValue');
                $this->registerClass($this->textvalue, 'TextValue');
                $this->registerClass($this->componentvalue, 'ComponentValue');
                $this->registerClass($this->contributorvalue, 'ContributorValue');
                
                $this->registerClass($this->scanner, 'Scanner');
                
                $this->addRelation('person.UserID', 'contributor.ContributorID');
                $this->addRelation('contributor.ContributorID', 'person.UserID');
                
		$this->addRelation('person.UserID', 'document.UserID');
		$this->addRelation('document.UserID', 'person.UserID');
                
                $this->addRelation('descriptor.DescriptorID', 'descriptorfield.DescriptorID');
                $this->addRelation('descriptorfield.DescriptorID', 'descriptor.DescriptorID');
                $this->addRelation('field.FieldID', 'descriptorfield.FieldID');
                $this->addRelation('descriptorfield.FieldID', 'field.FieldID');                                
                
                $this->addRelation('filevalue.ComponentID', 'component.ComponentID');
                $this->addRelation('filevalue.FieldID', 'field.FieldID');
                
                $this->addRelation('textvalue.ComponentID', 'component.ComponentID');
                $this->addRelation('textvalue.FieldID', 'field.FieldID');
                
                $this->addRelation('componentvalue.ComponentID', 'component.ComponentID');
                $this->addRelation('componentvalue.FieldID', 'field.FieldID');
                $this->addRelation('componentvalue.Value', 'component.ComponentID');
                
                $this->addRelation('contributorvalue.ComponentID', 'component.ComponentID');
                $this->addRelation('contributorvalue.FieldID', 'field.FieldID');
                $this->addRelation('contributorvalue.Value', 'contributor.ContributorID');
                
                $this->addRelation('component.DocumentID', 'document.DocumentID');
                $this->addRelation('document.DocumentID', 'component.DocumentID');
                
                $this->addRelation('component.DescriptorID', 'descriptor.DescriptorID');
                $this->addRelation('descriptor.DescriptorID', 'component.DescriptorID');
                
                $this->addRelation('descriptor.DescriptorID', 'scanner.DescriptorID');
                $this->addRelation('scanner.DescriptorID', 'descriptor.DescriptorID');
	}

	private $user = false;
        public function getUser()
        {
        	// Try to find the current user
	        if($this->user === false)
        	{
                	$file = tauFile::getCurrentFile();
                
	                if(!is_object($file))
        	        {
        			$this->user = null;
	                }
	                else
        	        {
                		$this->user = $file->getDecoration('user');
	                }
	        }
        	return $this->user;
        }
        
        public function setUser(DisaggregatorPerson $user)
        {
	        $this->user = $user;
        }
}
