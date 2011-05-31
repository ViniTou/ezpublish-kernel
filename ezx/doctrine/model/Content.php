<?php
/**
 * Content (content object) model object
 *
 * @copyright Copyright (c) 2011, eZ Systems AS
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2.0
 * @package ext
 * @subpackage doctrine
 */

/**
 * @Entity @Table(name="ezcontentobject")
 *
 * @property-read int $id
 * @property-read int $currentVersion
 * @property-read string $name
 * @property int $ownerId
 * @property int $sectionId
 * @property-read string $typeid Content Type Identifier
 * @property-read array(Location) $locations An hash like structure of fields
 * @property FieldMap(Field) $fields An hash structure of fields
 * @property-read ContentType $contentType Content type object
 */
namespace ezx\doctrine\model;
class Content implements Interface_Serializable
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $id = 0;

    /**
     * @Column(type="integer", name="current_version")
     * @var int
     */
    protected $currentVersion = 0;

    /**
     * @Column(length=255)
     * @var string
     */
    protected $name = '';

    /**
     * @Column(type="integer", name="owner_id")
     * @var int
     */
    protected $ownerId = 0;

    /**
     * @Column(type="integer", name="section_id")
     * @var int
     */
    protected $sectionId = 0;

    /**
     * @Column(type="integer", name="contentclass_id")
     * @var int
     */
    protected $typeId = 0;

    /**
     * @OneToMany(targetEntity="Location", mappedBy="content", fetch="EAGER")
     * @var SerializableCollection(Location)
     */
    protected $locations = array();

    /**
     * @OneToMany(targetEntity="Field", mappedBy="content", fetch="EAGER")
     * @var FieldMap(Field)
     */
    protected $fields = array();

    /**
     * @ManyToOne(targetEntity="ContentType", inversedBy="contentObjects")
     * @JoinColumn(name="contentclass_id", referencedColumnName="id")
     * @var ContentType
     */
    protected $contentType = 0;

    /**
     * Constructs a new instance of this class, protected, use factory on ContentRepository.
     */
    protected function __construct()
    {
        $this->locations = new SerializableCollection();
        $this->fields = new FieldMap();
    }

    /**
     * @throws \InvalidArgumentException
     * @param string $name
     * @return mixed
     */
    public function __get( $name )
    {
        if ( isset( $this->$name ) )
        {
            return $this->$name;
        }
        throw new \InvalidArgumentException( "{$name} is not a valid property on " . __CLASS__ );
    }

    /**
     * @throws \InvalidArgumentException
     * @param string $name
     * @param string $value
     * @return mixed Return $value
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'ownerId':
            case 'sectionId':
                $this->$name = $value;
                break;
            default:
                if ( isset( $this->$name ) )
                    throw new \InvalidArgumentException( "{$name} is not a writable property on " . __CLASS__ );
                else
                    throw new \InvalidArgumentException( "{$name} is not a valid property on " . __CLASS__ );
        }
    }

    /**
     * Used by var_export and other functions to init class with all values
     *
     * @static
     * @param array $properties
     * @return Content
     */
    final public static function __set_state( array $properties )
    {
        $class = new static();
        return $class->setState( $properties );
    }


    /**
     * Set properties with hash, name is same as used in ezc Persistent
     *
     * @param array $properties
     * @return Content Return $this
     */
    final public function setState( array $properties )
    {
        foreach ( $properties as $property => $value )
        {
            if ( $this->$property instanceof Interface_Serializable && !$value instanceof Interface_Serializable )
                $this->$property->setState( $value );
            else
                $this->$property = $value;
        }
        return $this;
    }

    /**
     * Get properties with hash, name is same as used in ezc Persistent
     *
     * @return array
     */
    final public function getState()
    {
        $hash = array();
        foreach( $this as $property => $value )
        {
            if ( $property instanceof Interface_Serializable )
                $hash[$property] = $value->getState();
            else
                $hash[$property] = $value;
        }
        return $hash;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id . ' ('  . $this->name . ')';
    }
}
