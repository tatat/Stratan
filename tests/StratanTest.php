<?php

class StratanTest extends PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    
  }

  protected function tearDown() {

  }

  /**
   * @test
   */
  public function should_set_and_get_value() {
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
  public function should_set_and_get_value_with_sepalator() {
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
  public function array_should_be_reference() {
    $array = array(
      'default' => array(
        'value' => array(
          'is' => 'set'
        )
      )
    );

    $object = new Stratan($array);

    $this->assertEquals('set', $object['default.value.is']);

    $array['default']['value']['is'] = 'changed';

    $this->assertEquals('changed', $object['default.value.is']);

    $object['new.value.is'] = 'set';

    $this->assertEquals('set', $array['new']['value']['is']);

    $object['new.value.is'] = 'changed';

    $this->assertEquals('changed', $array['new']['value']['is']);

    $part = $object['new'];
    $part->set('value.is', 'changed again');

    $this->assertEquals('changed again', $array['new']['value']['is']);

    $expected = array(
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

    $this->assertEquals($expected, $array);
    $this->assertEquals($expected, $object->to_array());
  }

  /**
   * @test
   */
  public function should_access_several_ways() {
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

    $this->assertEquals('set', $object->get('default.value1.is'));
    $this->assertEquals('set', $object['default.value2.is']);
    $this->assertEquals('set', $object->default->value3->is);

    $object->set('default.value1.is', 'changed');
    $object['default.value2.is'] = 'changed';
    $object->default->value3->is = 'changed';

    $this->assertEquals('changed', $object->get('default.value1.is'));
    $this->assertEquals('changed', $object['default.value2.is']);
    $this->assertEquals('changed', $object->default->value3->is);
  }

  /**
   * @test
   */
  public function should_set_default_values() {
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

    $this->assertEquals('already set', $object->get('default.value1.is'));
    $this->assertEquals('set', $object->get('default.value2.is'));
  }

  /**
   * @test
   */
  public function should_new_with_instance() {
    $other_object = new Stratan();

    $other_object->set(array(
      'default' => array(
        'value' => array(
          'is' => 'set'
        )
      )
    ));

    $object = new Stratan($other_object);

    $this->assertEquals('set', $object->get('default.value.is'));

    $object->set('new.value.is', 'set');

    $this->assertEquals('set', $other_object->get('new.value.is'));
  }

  /**
   * @test
   */
  public function should_set_with_instance() {
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

    $this->assertEquals('set', $object->get('default.value.is'));

    $object->set('new.value.is', 'set');

    $this->assertNull($other_object->get('new.value.is'));
  }

  /**
   * @test
   */
  public function should_return_default_value() {
    $object = new Stratan();
    $this->assertEquals('not set', $object->get('non-existent.key.is', 'not set'));
  }

  /**
   * @test
   */
  public function should_create_instance() {
    $object = Stratan::create(array(
      'value.is' => 'set'
    ));

    $this->assertInstanceOf('Stratan', $object);
    $this->assertEquals('set', $object['value.is']);
  }

  /**
   * @test
   */
  public function should_create_array() {
    $array = Stratan::create_array(array(
      'value1.is' => 'set',
      'value2.is' => 'set',
      'value3.is' => 'set'
    ));

    $expected = array(
      'value1' => array(
        'is' => 'set'
      ),
      'value2' => array(
        'is' => 'set'
      ),
      'value3' => array(
        'is' => 'set'
      )
    );

    $this->assertEquals($expected, $array);
  }

  /**
   * @test
   */
  public function should_create_with_other_separator() {
    $object = Stratan::create(array(
      'value.1/is' => 'set',
      'value.2/is' => 'set',
      'value.3/is' => 'set'
    ), '/');

    $expected = array(
      'value.1' => array(
        'is' => 'set'
      ),
      'value.2' => array(
        'is' => 'set'
      ),
      'value.3' => array(
        'is' => 'set'
      )
    );

    $this->assertEquals($expected, $object->to_array());
  }
}
