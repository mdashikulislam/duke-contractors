<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Carbon\Carbon;
use PhpOffice\PhpWord\PhpWord;

class DocController extends Controller
{
    public function index($id)
    {
        $lead = Lead::with('jobTypes')->whereHas('jobTypes')->where('id',$id)->first();
        if (empty($lead)){
            return response()->json([
                'status' => false,
                'message' => 'Lead not found',
                'data' => null
            ]);
        }
        $headers = array(
            "Content-type"=>"text/html",
            "Content-Disposition"=>"attachment;Filename=myGeneratefile.doc"
        );
        $content = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Email-Templete</title>
                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">


                <!-- common css start  -->
                <style>
                    *{
                    margin:0;
                    padding:0;
                    box-sizing:border-box;
                    }
                    a {
                        text-decoration: none;
                    }
                    p{
                        margin: 0;
                    }
                    body{
                        font-family: Inter, sans-serif;
                    }
                    ul {
                        list-style:none;
                        list-style-type: none;
                        padding:0;
                        margin:0;
                    }
            </style>
                </head>
                <body style="padding-left: 30px;padding-right: 30px;" >
                    <div id="container" align="center" >
                    <table style="width: 595px">
                        <tr>
                            <td>
                            <table>
                            <tr>
                                <td>
                                    <table>
                                        <tr>
                                            <td><img style="width: 80px;" src="'.asset('img/02.png').'" alt="logo"></td>
                                            <td>
                                                <p style="font-size: 16px; font-weight: 400; margin-bottom: 10px;">State License & Insured</p>
                                                <p style="font-size: 16px; font-weight: 400; margin-bottom: 10px;">#CCC 1325931</p>
                                                <p style="font-size: 16px; font-weight: 600; color: #F35810; margin-bottom: 10px;">10763 NW 23rd ST</p>
                                                <p style="font-size: 16px; font-weight: 600; color: #F35810;">Miami, FL 33172</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="text-align: center;">
                                 <img src="'.asset('img/01.png').'" alt="logo">
                                </td>
                                <td>
                                    <table style="
                                    text-align: end;
                                    width: 100%;
                                ">
                                        <tr>
                                            <td>
                                                <p style="font-size: 16px; font-weight: 600; color: #F35810; margin-bottom: 10px; margin-right: 68px;">Elias Issa</p>
                                                <p style="font-size: 16px; margin-bottom: 10px;"><span style="font-weight: 600; color: #F35810;">T:</span>  (267) 665-9909</p>
                                                <p style="font-size: 16px; margin-bottom: 10px;"><span style="font-weight: 600; color: #F35810;">O:</span>  (786) 468-7663 </p>
                                            </td>
                                            <td style="
                                            width: 100px;
                                        "><img style="width:100%" src="'.asset('img/03.png').'" alt="logo"></td>

                                        </tr>

                                    </table>
                                    <table style="text-align: end; width: 100%; margin-top: -15px;">
                                        <tr>
                                            <td >
                                                <p style="font-size: 13px; font-weight: 600; color: #F35810; margin-right: 107px;">GOT-ROOF</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 14px; margin-top: -5px;"><span style="font-weight: 600; font-size: 16px; color: #F35810;">E:</span> accounting@dukecontractors.net
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <table style="width: 920px; border: 1px solid #333; border-collapse: collapse; background: #FBE4D5;">
                            <tr style="border-bottom: 1px solid #333;">
                                <td style="width: 50%; padding: 3px 10px; border-right: 1px solid black;">
                                    <p style="font-size: 14px; line-height: 22px; font-weight: 500;"><span style="font-weight: 600;">NAME:</span>  '.$lead->customer_name.'</p>
                                </td>
                                <td style="width: 50%;">
                                    <table style="border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 25%; padding: 3px 10px; border-right: 1px solid black;">
                                                <p style="font-size: 14px; line-height: 22px; font-weight: 500;"><span style="font-weight: 600;">DATE:</span>  '.Carbon::parse($lead->created_at)->format('d-m-Y').'</p>
                                            </td>
                                            <td style="width: 25%; padding: 3px 10px;">
                                                <p style="font-size: 14px; line-height: 22px; font-weight: 500;"><span style="font-weight: 600;">CONTRACT</span> #'.(100000 + $lead->id).'</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #333;">
                                <td style="padding: 3px 10px;">
                                    <table>
                                        <tr><td>
                                            <p style="font-size: 14px; line-height: 22px; font-weight: 500;"><span style="font-weight: 600;">ADDRESS:</span>  '.@$lead->address.'</p>
                                        </td></tr>
                                    </table>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #333;">
                                <td style="padding: 3px 10px; border-right: 1px solid black;">
                                    <p style="font-size: 14px; line-height: 22px; font-weight: 500;"><span style="font-weight: 600;">PHONE NUMBER: </span>  '.@$lead->phone.' </p>
                                </td>
                                <td style="padding: 3px 10px;">
                                    <p style="font-size: 14px; line-height: 22x; font-weight: 500;"><span style="font-weight: 600;">EMAIL:</span> '.@$lead->email.'</p>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #333;">
                                <td style="padding: 3px 10px;">
                                    <p style="font-size: 14px; line-height: 22px; font-weight: 500;"><span style="font-weight: 600;">JOB DESCRIPTION:</span>  '.implode(', ',$lead->jobTypes->pluck('name')->toArray()).'</p>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid black;">
                                <td style="width: 50%; padding: 3px 10px; border-right: 1px solid black;">
                                    <p style="font-size: 14px; line-height: 22px; font-weight: 600; text-align: center;">ROOF MEASURES</p>
                                </td>
                                <td style="width: 50%; padding: 3px 10px;">
                                    <p style="font-size: 14px; line-height: 22px; font-weight: 600; text-align: center;">TERMS UPON AGREEMENT</p>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #333;">
                                <td>
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 50%; border-right: 1px solid #333; padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center;">Flat Roof</p>
                                            </td>
                                            <td style="width: 50%; border-right: 1px solid #333; padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center; text-decoration: underline;">300 Sq. ft.</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 50%; border-right: 1px solid #333; padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center;">20 % At the signing</p>
                                            </td>
                                            <td style="width: 50%;  padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center; ">$3,390.00</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #333;">
                                <td>
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 50%; border-right: 1px solid #333; padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center;">Sloped Roof</p>
                                            </td>
                                            <td style="width: 50%; border-right: 1px solid #333; padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center; text-decoration: underline;">1,700 Sq. ft.</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 50%; border-right: 1px solid #333; padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center;">30% At Job Commencement</p>
                                            </td>
                                            <td style="width: 50%;  padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center; ">$5,085.00</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #333;">
                                <td>
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 50%; border-right: 1px solid #333; padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center;">Total Roof Area</p>
                                            </td>
                                            <td style="width: 50%; border-right: 1px solid #333; padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center; text-decoration: underline;">2,000 Sq. ft.</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 50%; border-right: 1px solid #333; padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center;">30% After In-Progress Inspection</p>
                                            </td>
                                            <td style="width: 50%;  padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center; ">$5,085.00</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 50%; border-right: 1px solid #333; padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center;">Slope</p>
                                            </td>
                                            <td style="width: 50%; border-right: 1px solid #333; padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center; text-decoration: underline;">3/12 --- 1/12</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 50%; border-right: 1px solid #333; padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center;">20% At Final Inspection</p>
                                            </td>
                                            <td style="width: 50%;  padding: 3px 0px;">
                                                <p style="font-size: 13px; line-height: 22px; font-weight: 600; text-align: center; ">$3,390.00</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <table style="width: 920px; margin-top: 30px">
                            <tr>
                                <td style="padding-bottom: 15px;">
                                    <p style="font-size: 14px; font-weight: 600; text-decoration: underline;">WE ARE PLEASED TO SUBMIT THE FOLLOWING PROPOSAL & CONTRACT SPECIFICATIONS</p>
                                    <p style="font-size: 13px; font-weight: 400;">The following is a scope of works and subsequent pricing for a <span style="font-size: 14px; font-weight: 600;">RE ROOF</span> on the job mentioned above:</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 100%; padding-bottom: 15px;">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td style="width: 60%;">
                                                <p style="font-size: 14px; font-weight: 600; text-decoration: underline; font-style: italic;">- Roof Type: Three-Tab Shingle and Flat</p>
                                            </td>
                                            <td>
                                                <p style="font-style: italic;">*<span style="font-size: 14px; font-weight: 600; text-decoration: underline; font-style: italic;">Deck:</span> Wood.</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <table style="width: 920px;">
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px;">1.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px;">Remove the existing Shingle and flat roof to wood deck and to a clean workable surface. </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px; margin-top: -20px;">2.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px;"><span style="font-weight: 600;">Change all rotten wood;</span> the first <span style="color: #F35810; font-weight: 600;">6 sheets or 192 feet of plywood</span> free and after there will be a charge
                                        of carpentry cost and material cost. <span style="font-weight: 600;">($6 per square feet).</span>
                                         </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px; margin-top: -20px;">3.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px;"><span style="font-weight: 600;">	There will be </span> <span style="color: #F35810; font-weight: 600;">30 feet of fascia included</span> in this contract. If there is any additional fascia to be replaced
                                        there will be an additional cost of
                                         <span style="font-weight: 600;">$6 per lineal foot.</span> <span style="font-size: 18px; font-weight: 600; color: #C00000;">(Soffit not included)</span>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px;">4.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px;">Installation of 1X2 furring strips around the houses is included in this price if applicable. </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px;">5.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px;">Install one<span style="font-weight: 600;">	#30-anchor sheet </span> by <span style="color: #F35810; font-weight: 600;">TAMKO.</span>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px;">6.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px;">Install <span style="color: #F35810; font-weight: 600; font-size: 16px;">Timberline® HDZ™ or Timberline HDZ® Harvest</span>   by<span style="font-weight: 600;">	GAF  </span> on the entire roof.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px;">7.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px;"><span style="font-weight: 600;">	Install #75   </span>base paper nailed to wood deck.  <span style="color: #FF0000; font-weight: 600; font-size: 16px;">(Flat Roof Procedure)</span>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px;">8.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px;">Install <span style="color: #F35810; font-weight: 600; font-size: 16px;">Ruberoid #20 Smooth Underlayment</span>   by<span style="font-weight: 600; color: #F35810;">	GAF  </span> throughout entire flat roof with hot asphalt.
                                    </p>
                                </td>
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px;">9.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px;">Install <span style="color: #F35810; font-weight: 600; font-size: 16px;">GAF Cap Sheet</span>   throughout entire flat roof with hot asphalt.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px;">10.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px;"> <span style=" font-weight: 600;">Install</span> 3” x 3” eave drip, angle flashing, gooseneck ventilation system, valley metal and stucco stop where applicable.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px;">11.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px;">Change all roof lead flashing.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px;">12.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px;">Carry away all debris.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px; font-weight: 600;">13.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px; font-weight: 600">This proposal includes permits, insurance, labor, and materials.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px; font-weight: 600;">14.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px; font-weight: 600">Project manager on daily basis will enforce OSHA requirements.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20px;">
                                    <p style="font-size: 15px; width: 25px; font-weight: 600;">15.</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; line-height: 24px; font-weight: 600;">Everything will be done according to South Florida Building Code.
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <table style="width: 920px; margin-top: 30px">
                            <tr>
                                <td>
                                    <p style="font-size: 15px; color: #F35810; font-weight: 600; text-decoration: underline; text-align: center;">*Duke Contractors will provide a Twelve <span style="color: #333; background: yellow;">(12)</span> year warranty against leaks on area of repairs in Shingle Roof.</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="font-size: 15px; color: #F35810; font-weight: 600; text-decoration: underline; text-align: center;">*Duke Contractors will provide a Ten  <span style="color: #333; background: yellow;">(12)</span> years warranty against leaks on flat roof. </p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="font-size: 15px; color: #F35810; font-weight: 600; text-decoration: underline; text-align: center; margin-bottom: 30px;">*We agree to perform and complete the work in a workman like manner for  <span style="color: #333; background: yellow;">$16,950.00 </span> </p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="font-size: 13px; font-weight: 600;">
                                        NOTES: We are not responsible for the removal or reinstallation of electrical cables, telephone cables, or satellite dishes. The removal or re-installation of the gutters is by owner’s expenses. We will exercise reasonable care but cannot be held responsible in any matter for damage to sidewalks, driveways, foliage’s, shrubbery, screens, septic pipes, paint and plaster, interior ceiling.
                                    </p>
                                </td>
                            </tr>
                        </table>
                            </td>
                        </tr>
                    </table>

                    </div>
                </body>
                </html>';
        return \Response::make($content,200, $headers);

    }
}
