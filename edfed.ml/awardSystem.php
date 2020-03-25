<?php
function awardSystem($row)
{
    /** Award display. Display only awards from the highest category */
    
    //generate awards
    $award1=$award2=$award3=$award4=$award5='';
    for ($i=1; $i < 6; $i++) {
        //decide max loops since stg 1 has only 4w. Rest all 5w
        if ($i==1) {
            $max=4;
        } else {
            $max=5;
        }

        for ($j=1; $j <= $max; $j++) {
            if ($row['stg'.$i.'w'.$j]!=0) {
                if ($i==1) {
                    $award1.='<i class="fas fa-star" style="color:#cd7f32" title="Bronze (stg'.$i.'w'.$j.')"></i>';
                }
                if ($i==2) {
                    $award2.='<i class="fas fa-star" style="color:#C0C0C0" title="Silver (stg'.$i.'w'.$j.')"></i>';
                }
                if ($i==3) {
                    $award3.='<i class="fas fa-star" style="color:#FFD700" title="Gold (stg'.$i.'w'.$j.')"></i>';
                }
                if ($i==4) {
                    $award4.='<i class="fas fa-star" style="color:#e5e4e2" title="Platinum (stg'.$i.'w'.$j.')"></i>';
                }
                if ($i==5) {
                    $award5.='<i class="fas fa-crown" style="color:#b9f2ff" title="Crown (stg'.$i.'w'.$j.')"></i>';
                }
            }
        }
    }

    //helps decide only the highest stage attended
    if ($award5!='') {
        return $award5;
    }
    if ($award4!='') {
        return $award4;
    }
    if ($award3!='') {
        return $award3;
    }
    if ($award2!='') {
        return $award2;
    }
    if ($award1!='') {
        return $award1;
    }
}