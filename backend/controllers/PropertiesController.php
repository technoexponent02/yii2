<?php
namespace backend\controllers;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Property;
use backend\models\PropertySearch;
use common\components\SiteHelpers;
use yii\helpers\VarDumper;
use yii\filters\AccessControl;
use common\models\User;
require_once(Yii::getAlias('@common')."/lib/yii2tcpdf/tcpdf.php");
use TCPDF;
/**
 * PropertyController implements the CRUD actions for Ads model.
 */
class PropertiesController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'overview', 'report'],
                        'allow' => true,
                        'roles' => ['approvePosts'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Property models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PropertySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionOverview()
    {
        //$searchModel = new PropertySearch();
        return $this->render('overview', [
            //'searchModel' => $searchModel
        ]);
    }
  
    public function actionReport()
    {
        $posted_data = Yii::$app->request->post();

        $from_date = ($posted_data['from_date'])? strtotime($posted_data['from_date']) : time()-(30*24*60*60);
        $to_date = ($posted_data['to_date'])? strtotime($posted_data['to_date']) : time();

        $phtml = $this->renderPartial('properties-reports', [
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);
        return $phtml;
        //create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Waarf');
        $pdf->SetTitle('Waarf- Properties Reports');
        $pdf->SetSubject('Waarf');
        $pdf->SetKeywords('Waarf');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins(NULL, 10, NULL);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 8, '', true);
        $pdf->AddPage();
        $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
        $pdf->writeHTMLCell(0, 0, '', '', $phtml, 0, 1, 0, true, '', true);
        $pdf->Output('waarf_properties_reports.pdf', 'I');
    }

    /**
     * Updates an existing Property model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    //public function actionUpdate($id)
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $admin_user = Yii::$app->user->identity;
        $posted_data = Yii::$app->request->post();
        //print_r(Yii::$app->request->post());
        $model->status = $posted_data['status'];
        $model->reason_id = $posted_data['reason_id'];
        $model->approved_by = $admin_user->id;
        $model->save();
        $status = $model->status;
        if ($status == 2)
        {
           SiteHelpers::sendNotification($model->user, "P", $model->id); 
        }
        else if ($status == 3)
        {
            SiteHelpers::sendNotification($model->user, "W", $model->id); 
            $email = '';
                    ($model->parent_id)? $email = $model->parent->email : $email = $model->user->email;
                    SiteHelpers::sendSiteEmails('W', $email, $model); 
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return  ['model' => $model];
     
        // return $this->render('update', [
        //                 'model' => $model,
        //             ]);
        
    }


    /**
     * Finds the Report model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Report the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Property::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
