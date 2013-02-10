<?php

class StratanTest extends PHPUnit_Framework_TestCase {
  
  protected function setUp() {

  }

  protected function tearDown() {

  }

  /**
   * @test
   */
  public function shoud_set_and_get_value() {
    $object = new Stratan();

    $object->set('using Stratan::set()', true);
    $object['using Stratan[]'] = true;
    $object[] = true;
    $object[] = true;

    $this->assertTrue($object->get('using Stratan::set()'));
    $this->assertTrue($object->get('using Stratan[]'));
    $this->assertTrue($object->get('0'));
    $this->assertTrue($object->get('1'));

    $this->assertTrue($object['using Stratan::set()']);
    $this->assertTrue($object['using Stratan[]']);
    $this->assertTrue($object['0']);
    $this->assertTrue($object['1']);
  }

  /**
   * @test
   */
  public function shoud_set_and_get_value_with_sepalator() {
    $object = new Stratan();

    $object->set('dot.separated.using Stratan::set()', true);
    $object['dot.separated.using Stratan[]'] = true;

    $this->assertArrayHasKey('dot', $object->to_array());

    $this->assertArrayHasKey('separated', $object->get('dot')->to_array());
    $this->assertArrayHasKey('using Stratan::set()', $object->get('dot.separated')->to_array());
    $this->assertArrayHasKey('using Stratan[]', $object->get('dot.separated')->to_array());
    $this->assertTrue($object->get('dot.separated.using Stratan::set()'));
    $this->assertTrue($object->get('dot.separated.using Stratan[]'));

    $this->assertArrayHasKey('separated', $object['dot']->to_array());
    $this->assertArrayHasKey('using Stratan::set()', $object['dot.separated']->to_array());
    $this->assertArrayHasKey('using Stratan[]', $object['dot.separated']->to_array());
    $this->assertTrue($object['dot.separated.using Stratan::set()']);
    $this->assertTrue($object['dot.separated.using Stratan[]']);
  }

  /**
   * @test
   */
  public function array_shoud_be_reference() {
    $array = array(
      'default' => array(
        'value' => array(
          'is' => 'set'
        )
      )
    );

    $object = new Stratan($array);

    $this->assertEquals($object['default.value.is'], 'set');

    $array['default']['value']['is'] = 'changed';

    $this->assertEquals($object['default.value.is'], 'changed');

    $object['new.value.is'] = 'set';

    $this->assertEquals($array['new']['value']['is'], 'set');

    $object['new.value.is'] = 'changed';

    $this->assertEquals($array['new']['value']['is'], 'changed');

    $part = $object['new'];
    $part->set('value.is', 'changed again');

    $this->assertEquals($array['new']['value']['is'], 'changed again');

    $result = array(
      'default' => array(
        'value' => array(
          'is' => 'changed'
        )
      ),
      'new' => array(
        'value' => array(
          'is' => 'changed again'
        )
      )
    );

    $this->assertEquals($array, $result);
    $this->assertEquals($object->to_array(), $result);
  }

  /**
   * @test
   */
  public function shoud_access_several_ways() {
    $object = new Stratan();

    $object->set(array(
      'default' => array(
        'value1' => array(
          'is' => 'set'
        ),
        'value2' => array(
          'is' => 'set'
        ),
        'value3' => array(
          'is' => 'set'
        )
      )
    ));

    $this->assertEquals($object->get('default.value1.is'), 'set');
    $this->assertEquals($object['default.value2.is'], 'set');
    $this->assertEquals($object->default->value3->is, 'set');

    $object->set('default.value1.is', 'changed');
    $object['default.value2.is'] = 'changed';
    $object->default->value3->is = 'changed';

    $this->assertEquals($object->get('default.value1.is'), 'changed');
    $this->assertEquals($object['default.value2.is'], 'changed');
    $this->assertEquals($object->default->value3->is, 'changed');
  }

  /**
   * @test
   */
  public function shoud_set_default_values() {
    $object = new Stratan();

    $object->set(array(
      'default' => array(
        'value1' => array(
          'is' => 'already set'
        )
      )
    ));

    $object->set_defaults(array(
      'default.value1.is' => 'set',
      'default.value2.is' => 'set'
    ));

    $this->assertEquals($object->get('default.value1.is'), 'already set');
    $this->assertEquals($object->get('default.value2.is'), 'set');
  }

  /**
   * @test
   */
  public function shoud_new_with_instance() {
    $other_object = new Stratan();

    $other_object->set(array(
      'default' => array(
        'value' => array(
          'is' => 'set'
        )
      )
    ));

    $object = new Stratan($other_object);

    $this->assertEquals($object->get('default.value.is'), 'set');

    $object->set('new.value.is', 'set');

    $this->assertEquals($other_object->get('new.value.is'), 'set');
  }

  /**
   * @test
   */
  public function shoud_set_with_instance() {
    $other_object = new Stratan();

    $other_object->set(array(
      'default' => array(
        'value' => array(
          'is' => 'set'
        )
      )
    ));

    $object = new Stratan();
    $object->set($other_object);

    $this->assertEquals($object->get('default.value.is'), 'set');

    $object->set('new.value.is', 'set');

    $this->assertNull($other_object->get('new.value.is'));
  }

  /**
   * @test
   */
  public function shoud_return_default_value() {
    $object = new Stratan();
    $this->assertEquals($object->get('non-existent.key.is', 'not set'), 'not set');
  }
}
