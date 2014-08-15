<?php

/**
 * This is the model class for table "tokens".
 *
 * The followings are the available columns in table 'tokens':
 * @property integer $user_id
 * @property string $token
 * @property string $created
 *
 * The followings are the available model relations:
 * @property Users $user
 */
class Tokens extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Tokens the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tokens';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, token, created', 'required'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('token', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, token, created', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id' => 'User',
			'token' => 'Token',
			'created' => 'Created',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('token',$this->token,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	
	public function beforeValidate()
	{
	         $this->created = new CDbExpression('NOW()');
	         
	         return parent::beforeSave();
	}
	
	public function generateToken($id, $key)
	{        
	        return mb_strimwidth(hash("sha256", hash("sha256", hash("whirlpool", md5($id . md5(time())))) . hash("sha256", md5($id . md5($key))) . $key), 0, 32);	
	}
}
