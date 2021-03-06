<?php

/**
 * The MODEL of courses.
 * @author Marty Zhang
 * @version 0.9.201805071048
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model {

  use SoftDeletes; // To prevent a course being deleted by accident.

  const STATUS_INACTIVE = 0;
  const STATUS_ACTIVE = 1;
  const STATUS_DISCONTINUED = 2;

  /**
   * The attributes that are mass assignable.
   * @var array
   */
  protected $fillable = [
      'courseCode', 'courseName', 'courseDescription', 'coursePoints', 'status',
  ];
  protected $primaryKey = 'courseCode'; // Specifies a custom primary key column other than the default 'id'.
  public $incrementing = FALSE; // Disables the default incrementing feature of the primary key.
  protected $keyType = 'string'; // If your primary key isn't an integer.
  protected $attributes = [
      'status' => self::STATUS_ACTIVE, // Gives the course status a default value.
  ];

  /**
   * The attributes that should be mutated to dates.
   * @var array
   */
  protected $dates = ['deleted_at'];

  /**
   * Gets the students that have/were enrolled into the course.
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function students() {
    return $this->belongsToMany('App\Student', 'students_courses', 'cId', 'sId', 'id', 'id') // Development Note: The last 4 parameters are necessary because the foreign key constraints in the actual relationship database table are not referencing the primary key fields of the Models, which violates the default values of belongsToMany().
                    ->withPivot('id', 'semester', 'status')
                    ->withTimestamps()
                    ->using('App\StudentsCourses');
  }

  /**
   * Checks if the given status is an acceptable value.
   * @param integer $status The course status.
   * @return boolean TRUE is the given status is an acceptable value; or FALSE otherwise.
   */
  public static function isStatusValid($status) {
    return in_array($status, [
        self::STATUS_INACTIVE,
        self::STATUS_ACTIVE,
        self::STATUS_DISCONTINUED,
    ]);
  }

  /**
   * Gets the textual meaning of the course status number.
   * @return string The textual meaning of the course status number; or a warning message otherwise.
   */
  public function getStatusText() {
    $result = '';

    switch ($this->status) {
      case self::STATUS_INACTIVE:
        $result = 'Inactive';
        break;

      case self::STATUS_ACTIVE:
        $result = 'Active';
        break;

      case self::STATUS_DISCONTINUED:
        $result = 'Discontinued';
        break;

      default:
        $result = "The course status number '{$this->status}' cannot be recognised.";
    }

    return $result;
  }

}
