<?php

/**
 * This is the model class for table "developers".
 *
 * The followings are the available columns in table 'developers':
 * @property integer $id
 * @property integer $user_id
 * @property integer $developer_status
 * @property string $created
 * @property string $updated
 *
 * The followings are the available model relations:
 * @property Applications[] $applications
 * @property DeveloperStatuses $developerStatus
 * @property Users $user
 */
class Developers extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Developers the static model class
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
		return 'developers';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, developer_status, created, updated', 'required'),
			array('user_id, developer_status', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, developer_status, created, updated', 'safe', 'on'=>'search'),
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
			'applications' => array(self::HAS_MANY, 'Applications', 'developer_id'),
			'developerStatus' => array(self::BELONGS_TO, 'DeveloperStatuses', 'developer_status'),
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'developer_status' => 'Developer Status',
			'created' => 'Created',
			'updated' => 'Updated',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('developer_status',$this->developer_status);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	public function beforeValidate()
	{
	        if ($this->isNewRecord)
                        $this->created = new CDbExpression('NOW()');
                        
	        $this->updated = new CDbExpression('NOW()');	         
	        return parent::beforeSave();
	}
}
