<?php 

namespace Ediasep\Bowler\Helper;

use Illuminate\Support\Facades\DB;
use Illuminate\Filesystem\Filesystem;

class BowlerHelper 
{
	/**
	 * FileSystem Object
	 *
	 * @var string
	 **/
	protected $files;

	/**
	 * Dummy stub file path
	 *
	 * @var string
	 **/
	protected $stub_path;

	/**
	 * Generated migrations file path
	 * app/database/migrations
	 *
	 * @var string
	 **/
	protected $migration_path;

	/**
	 * Datatype Mapping
	 *
	 * @var string
	 * bigIncrements, Increments, Morphs, nullableTimeStamps
	 * softDeletes, string with length, timestamp, rememberToken
	 **/
	protected $fieldtype = [  
		'blob'      => 'binary', 
		'bigint'    => 'bigInt', 
		'boolean'   => 'boolean',
		'char'      => 'char',
		'date'      => 'date',
		'datetime'  => 'datetime',
		'decimal'   => 'decimal',
		'double'    => 'double',
		'enum'      => 'enum',
		'float'     => 'float',
		'int'       => 'integer',
		'longtext'  => 'longtext',
		'mediumint' => 'mediumInteger',
		'mediumtext'=> 'mediumText',
		'smallint'  => 'smallInteger',
		'tinyint'   => 'tinyInteger',
		'varchar'   => 'string',
		'text'      => 'text',
		'time'      => 'time',
		'timestamp' => 'timestamp' ];

    /**
     * Create a new instance.
     * @param Illuminate\Filesystem\Filesystem $files
     */
	function __construct(Filesystem $files)
	{
		$this->files          = $files;
		$this->stub_path      = __DIR__.'/../Stub/';
		$this->migration_path = database_path().'/migrations/';
	}

	/**
	 * get all table field into an array
	 *
	 * @return array
	 * @author Asep Edi Kurniawan
	 **/
	public function getTableFields($database, $table)
	{
		/**
		* Select table field from database based on database
		* name and table inputed from terminal 
		**/

		// MySQL
		$fields = DB::select("SELECT * FROM information_schema.columns WHERE table_schema = ? AND table_name = ?", [ $database, $table ]);

		return $fields;
	}

	/**
	 * Create migration from given field list
	 *
	 * @return String $new_file
	 * @author Asep Edi Kurniawan
	 **/
	public function createMigration($fields, $table)
	{
		$field_string = $this->generateFieldList($fields);
		$new_file     = $this->generateMigrationFilename($table);

        $this->replaceAndSave($this->stub_path.'Migration.stub', [ '{{table}}', '{{Table}}', '{{fields}}' ], [ $table, ucwords($table), $field_string ], $new_file);

        return basename($new_file);
	}

	/**
	 * Generate Migration Filename
	 *
	 * @return string
	 * @author Asep Edi Kurniawan
	 **/
	public function generateMigrationFilename($table)
	{
		$prefix   = date('Y_m_d_His');
		$filename = $this->migration_path.sprintf('%s_create_%s_table', $prefix, $table).'.php';

		return $filename;
	}

	/**
	 * Generate field list in string format
	 *
	 * @return String
	 * @author Asep Edi Kurniawan
	 **/
	public function generateFieldList($fields)
	{
		$field_string = "";

		$i = 1;
		foreach ($fields as $field) {
			if ($i != 1)
				$field_string .= "\n\t\t\t";

			$field_string .= sprintf("\$table->%s('%s');", $this->fieldtype[ $field->DATA_TYPE ], $field->COLUMN_NAME);
			$i++;
		}

		return $field_string;
	}

	/**
	 * Replace stub file content
	 *
	 * @param string $oldFile
	 * @param string[] $search
	 * @param string $newFile
	 * @return void
	 * @author Asep Edi Kurniawan
	 **/
    public function replaceAndSave($oldFile, $search, $replace, $newFile = null)
    {
        $newFile   = ($newFile == null) ? $oldFile : $newFile;
        $file      = $this->files->get($oldFile);
        $replacing = str_replace($search, $replace, $file);

        $this->files->put($newFile, $replacing);
    }
} // END class BowlerHelper 