<?php

namespace yeesoft\models\search;

use yeesoft\models\Menu;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchMenu represents the model behind the search form about `frontend\models\Menu`.
 */
class SearchMenu extends Menu
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'title'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Menu::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}