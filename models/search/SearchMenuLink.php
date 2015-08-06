<?php

namespace yeesoft\models\search;

use yeesoft\models\MenuLink;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchMenuLink represents the model behind the search form about `yeesoft\menu\models\MenuLink`.
 */
class SearchMenuLink extends MenuLink
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order'], 'integer'],
            [['id', 'menu_id', 'parent_id', 'link', 'label', 'image'], 'safe'],
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
        $query = MenuLink::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'order' => $this->order,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'menu_id', $this->menu_id])
            ->andFilterWhere(['like', 'parent_id', $this->parent_id])
            ->andFilterWhere(['like', 'image', $this->image]);

        return $dataProvider;
    }
}