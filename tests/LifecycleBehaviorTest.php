<?php

class LifecycleBehaviorTest extends \PHPUnit\Framework\TestCase
{
    protected function createPost()
    {
        $post = new Post();
        $post->status = 'draft';
        $result = $post->validate();
        $this->assertTrue($result, print_r($post->getErrors(), true));
        $this->assertTrue($post->save(false));
        return $post;
    }

    public function testStatusChangeValid()
    {
        $post = $this->createPost();
        $post->status = 'ready';
        $result = $post->validate();
        $this->assertTrue($result, print_r($post->getErrors(), true));
        $this->assertTrue($post->save(false));
    }

    public function testStatusChangeInValid()
    {
        $post = $this->createPost();
        $post->status = 'archived';
        $result = $post->validate();
        $this->assertFalse($result);
        $this->assertTrue($post->hasErrors('status'));
        $this->assertEquals([
            'Invalid status change: "draft" to "archived"'
        ], $post->getErrors('status'));
    }

    public function testStatusChangeInValidException()
    {
        $post = $this->createPost();
        $post->status = 'archived';
        $this->expectException(\cebe\lifecycle\StatusChangeNotAllowedException::class);
        $this->expectExceptionMessage('Invalid status change: "draft" to "archived"');
        $post->save(false);
    }
}

class Post extends \yii\db\ActiveRecord
{
    /**
     * @var \yii\db\Connection
     */
    public static $db;
    public static function getDb()
    {
        if (static::$db === null) {
            static::$db = new \yii\db\Connection([
                'dsn' => 'sqlite::memory:',
                'schemaCache' => false,
                'enableLogging' => false,
            ]);
            static::$db->createCommand()->createTable('post', [
                'id' => 'pk',
                'status' => 'string',
            ])->execute();
        }
        return static::$db;
    }

    public function behaviors()
    {
        return [
            'lifecycle' => [
                'class' => cebe\lifecycle\LifecycleBehavior::class,
                'validStatusChanges' => [
                    'draft'     => ['ready', 'delivered'],
                    'ready'     => ['draft', 'delivered'],
                    'delivered' => ['payed', 'archived'],
                    'payed'     => ['archived'],
                    'archived'  => [],
                ],
            ],
        ];
    }
}
