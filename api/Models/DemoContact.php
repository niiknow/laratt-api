<?php
namespace Api\Models;

use Illuminate\Database\Eloquent\Model;

class DemoContact extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'photo_url',
        'address1',
        'address2',
        'city',
        'state',
        'postal',
        'country',
        'phone',
        'occupation',
        'employer',
        'note',
        'lat',
        'lng'
    ];

    /**
     * @var string
     */
    protected $table = 'a0$demo_contacts';
}
