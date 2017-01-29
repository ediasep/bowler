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
     * Generated seeder file path
     * app/database/seeds
     *
     * @var string
     **/
    protected $seed_path;

    /**
     * Datatype Mapping
     *
     * @var string
     * Some type not included:
     * bigIncrements, Morphs, nullableTimeStamps
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
        $this->seed_path      = database_path().'/seeds/';
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

            // Check if field type is increment
            $field_type = $this->isIncrement($field) ? 'increments' : $this->fieldtype[ $field->DATA_TYPE ];

            // Check if field has length attribute
            $field_string .= $this->hasLength($field) ?
                             $this->fieldStringWithLength($field, $field_type) : $this->fieldStringNoLength($field, $field_type);

            $field_string .= ';';

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
        $newFile   = ($newFile === null) ? $oldFile : $newFile;
        $file      = $this->files->get($oldFile);
        $replacing = str_replace($search, $replace, $file);

        $this->files->put($newFile, $replacing);
    }

    /**
     * Generate field string with length arg
     *
     * @param string $field_type
     * @return String
     * @author Asep Edi Kurniawan
     **/
    public function fieldStringWithLength($field, $field_type)
    {
        $field_string = sprintf(
                "\$table->%s('%s', %d)", 
                $field_type, 
                $field->COLUMN_NAME, 
                $field->CHARACTER_MAXIMUM_LENGTH
        );

        return $field_string;
    }


    /**
     * Generate field string without length arg
     *
     * @param string $field_type
     * @return String
     * @author Asep Edi Kurniawan
     **/
    public function fieldStringNoLength($field, $field_type)
    {
        $field_string = sprintf(
            "\$table->%s('%s')", 
            $field_type, 
            $field->COLUMN_NAME
        );

        return $field_string;
    }

    /**
     * Check whether the field is autoincrement
     *
     * @return Boolean
     * @author Asep Edi Kurniawan
     **/
    public function isIncrement($field)
    {
        if (!isset($field->EXTRA)) {
                    return false;
        }

        if ($field->EXTRA != 'auto_increment') {
            return false;
        }

        return true;
    }

    /**
     * Check whether the field has length attribute
     *
     * @return Boolean
     * @author Asep Edi Kurniawan
     **/
    public function hasLength($field)
    {
        if (!isset($field->CHARACTER_MAXIMUM_LENGTH)) {
                    return false;
        }

        if (empty($field->CHARACTER_MAXIMUM_LENGTH)) {
                    return false;
        }

        if ($field->DATA_TYPE == 'text') {
                    return false;
        }

        return true;
    }

    /**
     * Create Table for Seeder
     *
     * @return String
     * @author Asep Edi Kurniawan
     **/
    public function createSeeder($table)
    {
        $new_file       = $this->generateSeederFilename($table);
        $content_string = $this->generateSeederFieldList($table);

        $this->replaceAndSave($this->stub_path.'Seeder.stub', [ '{{table}}', '{{Model}}', '{{Content}}' ], [ $table, ucwords($table), $content_string ], $new_file);
    }

    /**
     * Generate Seeder Filename
     *
     * @return String
     * @author Asep Edi Kurniawan
     **/
    public function generateSeederFilename($table)
    {
        $filename = $this->seed_path.sprintf('%sTableSeeder.php', ucwords($table));
        return $filename;
    }

    /**
     * Generate Field List for Seeder
     *
     * @return String
     * @author Asep Edi Kurniawan
     **/
    public function generateSeederFieldList($table)
    {
        // Query
        $query = sprintf("SELECT * FROM %s", $table); 

        // MySQL
        $fields = DB::select($query);

        // Content String
        $content_string = "";

        // Field List
        foreach ($fields as $field) {

            // local var
            $j = 1;
            $column_count = count((array)$field);

            // Field String
            $field_string = "[\n\t\t\t";
            foreach ($field as $key => $value) {
                $line_breaker  = ($j < $column_count) ? "',\n\t\t\t" : "',\n\t\t";
                $field_string .= "'$key'"." => '". trim($value) . $line_breaker;
                $j++;
            }

            $field_string .= "]";

            $content_string .= sprintf("DB::table('%s')->insert(%s);\n\n\t\t", $table, $field_string);
        }

        return $content_string;
    }

} // END class BowlerHelper 