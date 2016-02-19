<?php

class GroupHelper
{
    public static function checkGroup($record, DisaggregatorPerson $requester)
    {
        if($record->Security == "Group")
        {
            $model = DisaggregatorModel::get();
            $group = $model->usergroup->getRecordByPK($record->GroupID);
            $gps = $group->getgrouppersons();
            $gpids = $gps->ArrayOf('PersonID');
            if(in_array($requester->UserID, $gpids))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }       
}


