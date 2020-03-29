<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;
use Illuminate\Support\Facades\DB;

class BulkCostingFeatureDetails extends BaseValidator {

    protected $table = 'costing_bulk_feature_details';
    protected $primaryKey = 'blk_feature_id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';


    protected $fillable = ['mcq'];

    static function getEmpNp($product_feature_id,$data) {

        $blk=$data['blkNo'];
        $bom=$data['bom'];
        $season=$data['season'];
        $colType=$data['colType'];


        $getTotel=DB::select('SELECT
Sum((costing_bulk_details.unit_price*costing_bulk_details.gross_consumption)) AS total,
Sum(costing_bulk_feature_details.smv) AS smv,
costing_bulk.fob,
costing_bulk.plan_efficiency
FROM
costing_bulk_feature_details
INNER JOIN costing_bulk_details ON costing_bulk_details.bulkheader_id = costing_bulk_feature_details.blk_feature_id
INNER JOIN costing_bulk ON costing_bulk.bulk_costing_id = costing_bulk_feature_details.bulkheader_id
WHERE costing_bulk.bulk_costing_id='.$blk.' AND costing_bulk_feature_details.style_feature_id='.$product_feature_id.' AND costing_bulk_feature_details.season_id='.$season.' AND costing_bulk_feature_details.col_opt_id='.$colType.' AND costing_bulk_feature_details.bom_stage='.$bom.' AND costing_bulk_details.status=1');

        $rmCost=0;$smv=0;$fob=0;$epm=0;$labourCost=0;$cpm=0;$totalManuf=0;$finCost=0;$copCost=0;$totalCost=0;$np=0;
        if($getTotel[0]->total!=''){
            $rmCost=$getTotel[0]->total;
        }
        if($getTotel[0]->smv!=''){
            $smv=$getTotel[0]->smv;
        }
        if($getTotel[0]->fob!=''){
            $fob=$getTotel[0]->fob;
        }
        if($smv !=0){
            $epm=($fob-$rmCost)/$smv;
        }

        $financeCost=\App\Models\Finance\Cost\FinanceCost::first();
        $cpm=($getTotel[0]->plan_efficiency*$financeCost->cpum);
        $labourCost=$smv*$cpm;
        $totalManuf=$rmCost+$labourCost;
        $finCost=$financeCost->finance_cost;
        $copCost=$smv*$financeCost->cpmfront_end;
        $totalCost=$rmCost+$totalManuf+$finCost+$copCost;
        $totalCostWithoutRm=$totalManuf+$finCost+$copCost;

        if($totalCost !=0){
            $np=($totalCost-$fob)/$totalCost;
        }

        return array('epm'=>$epm,'np'=>$np,'totalCostWithoutRm'=>$totalCostWithoutRm,'fob'=>$fob );

    }

}
