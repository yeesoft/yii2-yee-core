<?php

namespace yeesoft\controllers;

use Yii;
use yii\web\Cookie;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yeesoft\db\ActiveRecord;
use yeesoft\db\FilterableQuery;

abstract class CrudController extends BaseController
{

    /**
     * @var ActiveRecord
     */
    public $modelClass;

    /**
     * @var ActiveRecord
     */
    public $modelSearchClass;

    /**
     * @var string
     */
    public $modelPrimaryKey;

    /**
     * Actions that will be disabled.
     *
     * List of available actions:
     *
     * ['index', 'view', 'create', 'update', 'delete', 'toggle-attribute',
     * 'bulk-activate', 'bulk-deactivate', 'bulk-delete', 'grid-sort', 'grid-page-size']
     *
     * @var array
     */
    public $disabledActions = [];

    /**
     * Opposite to $disabledActions. Actions not listed in the array will be disabled.
     *
     * Action listed both in $disabledActions and $enabledOnlyActions will be disabled.
     *
     * @var array
     */
    public $enabledOnlyActions = [];

    /**
     * Layout file for admin panel
     *
     * @var string
     */
    public $layout = '@vendor/yeesoft/yii2-yee-core/views/layouts/main';

    /**
     * Index page view
     *
     * @var string
     */
    public $indexView = 'index';

    /**
     * View page view
     *
     * @var string
     */
    public $viewView = 'view';

    /**
     * Create page view
     *
     * @var string
     */
    public $createView = 'create';

    /**
     * Update page view
     *
     * @var string
     */
    public $updateView = 'update';

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->modelPrimaryKey === null) {
            $modelClass = $this->modelClass;
            $primaryKey = $modelClass::primaryKey();

            if (isset($primaryKey[0])) {
                $this->modelPrimaryKey = $primaryKey[0];
            } else {
                throw new InvalidConfigException('"' . $modelClass . '" must have a primary key.');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
                    'verbs' => [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            'delete' => ['post'],
                        ],
                    ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {

            if (!empty($this->enabledOnlyActions) && !in_array($action->id, $this->enabledOnlyActions)) {
                throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
            }

            if (in_array($action->id, $this->disabledActions)) {
                throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
            }

            return true;
        }

        return false;
    }

    /**
     * Define redirect page after update, create, delete, etc
     *
     * @param string $action
     * @param ActiveRecord $model
     *
     * @return string|array
     */
    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'delete':
                return ['index'];
                break;
            case 'update':
                return ['view', 'id' => $model->{$this->modelPrimaryKey}];
                break;
            case 'create':
                return ['view', 'id' => $model->{$this->modelPrimaryKey}];
                break;
            default:
                return ['index'];
        }
    }

    /**
     * Lists all models.
     * @return mixed
     */
    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        $searchModel = $this->modelSearchClass ? new $this->modelSearchClass : null;

        if ($searchModel) {
            $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        } else {
            $query = $modelClass::find();

            if ($query instanceof FilterableQuery) {
                $query->applyFilters();
            }

            $dataProvider = new ActiveDataProvider(['query' => $query]);
        }

        return $this->renderIsAjax($this->indexView, compact('dataProvider', 'searchModel'));
    }

    /**
     * Displays a single model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->renderIsAjax($this->viewView, [
                    'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        /* @var $model \yeesoft\db\ActiveRecord */
        $model = new $this->modelClass;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('yee', 'Your item has been created.'));
            return $this->redirect($this->getRedirectPage('create', $model));
        }

        return $this->renderIsAjax($this->createView, compact('model'));
    }

    /**
     * Updates an existing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        /* @var $model \yeesoft\db\ActiveRecord */
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) AND $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('yee', 'Your item has been updated.'));
            return $this->redirect($this->getRedirectPage('update', $model));
        }

        return $this->renderIsAjax($this->updateView, compact('model'));
    }

    /**
     * Deletes an existing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        /* @var $model \yeesoft\db\ActiveRecord */
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('success', Yii::t('yee', 'Your item has been deleted.'));
        return $this->redirect($this->getRedirectPage('delete', $model));
    }

    /**
     * @param string $attribute
     * @param int $id
     */
    public function actionToggleAttribute($attribute, $id)
    {
        /* @var $model \yeesoft\db\ActiveRecord */
        $model = $this->findModel($id);

        if (!$model->hasAttribute($attribute)) {
            throw new InvalidParamException('Model has no attribute with the name "' . $attribute . '"');
        }

        $model->{$attribute} = ($model->{$attribute} == 1) ? 0 : 1;
        $model->save(false);
    }

    /**
     * Activate all selected grid items.
     * 
     * @throws \Exception
     */
    public function actionBulkActivate()
    {
        $selection = Yii::$app->request->post('selection');
        /* @var $model \yeesoft\db\ActiveRecord */
        $this->bulkAction($selection, function($model) {
            if (!$model->hasAttribute('status')) {
                throw new InvalidParamException('Model has no attribute with the name "status"');
            }

            $model->status = 1;
            $model->save(false);
        });
    }

    /**
     * Deactivate all selected grid items.
     * 
     * @throws \Exception
     */
    public function actionBulkDeactivate()
    {
        $selection = Yii::$app->request->post('selection');
        $this->bulkAction($selection, function($model) {
            if (!$model->hasAttribute('status')) {
                throw new InvalidParamException('Model has no attribute with the name "status"');
            }

            $model->status = 0;
            $model->save(false);
        });
    }

    /**
     * Delete all selected grid items.
     * 
     * @throws \Exception
     */
    public function actionBulkDelete()
    {
        $selection = Yii::$app->request->post('selection');
        $this->bulkAction($selection, function($model) {
            /* @var $model \yeesoft\db\ActiveRecord */
            $model->delete();
        });
    }

    /**
     * Sorting items in grid
     */
    public function actionGridSort()
    {
        if (Yii::$app->request->post('sorter')) {
            $sortArray = Yii::$app->request->post('sorter', []);

            $modelClass = $this->modelClass;

            $models = $modelClass::findAll(array_keys($sortArray));

            foreach ($models as $model) {
                $model->sorter = $sortArray[$model->{$this->modelPrimaryKey}];
                $model->save(false);
            }
        }
    }

    /**
     * Set page size for grid
     */
    public function actionGridPageSize()
    {
        if (Yii::$app->request->post('grid-page-size')) {
            $cookie = new Cookie([
                'name' => '_grid_page_size',
                'value' => Yii::$app->request->post('grid-page-size'),
                'expire' => time() + 86400 * 365, // 1 year
            ]);

            Yii::$app->response->cookies->add($cookie);
        }
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param mixed $id
     *
     * @return ActiveRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $modelClass = $this->modelClass;
        $model = new $modelClass;

        $query = $modelClass::find()->andWhere([$this->modelPrimaryKey => $id]);

        if ($model instanceof ActiveRecord && $model->isMultilingual()) {
            $query->multilingual();
        }

        if ($query instanceof FilterableQuery) {
            $query->applyFilters();
        }

        if ($result = $query->one()) {
            return $result;
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    /**
     * Run bulk action.
     * 
     * @param array $selection
     * @param string $attribute
     * @param mixed $value
     * @throws \Exception
     */
    protected function bulkAction($selection, $action)
    {
        if (is_array($selection)) {
            /* @var $modelClass \yeesoft\db\ActiveRecord */
            $modelClass = $this->modelClass;

            /* @var $query \yeesoft\db\ActiveQuery */
            $query = $modelClass::find()->where([$this->modelPrimaryKey => $selection]);

            if ($query instanceof FilterableQuery) {
                $query->applyFilters();
            }

            $models = $query->all();
            $transaction = Yii::$app->db->beginTransaction();

            try {
                foreach ($models as $model) {
                    $action($model);
                }

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
    }

}
