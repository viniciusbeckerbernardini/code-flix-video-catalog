<?php

namespace Tests\Traits;
trait TestDates
{
    public function testDatesAttribute()
    {
        $dates = ['created_at','updated_at','deleted_at'];
        foreach ($dates as $date){
            $this->assertContains($date, $this->getModel()->getDates());
        }
        $this->assertCount(count($dates),$this->getModel()->getDates());
    }
}
