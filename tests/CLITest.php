<?php
require_once 'PHPUnit/Framework.php';
require_once '../Scisr.php';

/**
 * @runTestsInSeparateProcesses
 */
class CLITest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider ignoreOptProvider
     */
    public function testSetIgnore($args, $patterns) {
        $mock = $this->getMock('Scisr');
        $mock->expects($this->once())
            ->method('setRenameClass')
            ->with($this->equalTo('Foo'), $this->equalTo('Baz'));
        $mock->expects($this->once())
            ->method('setIgnorePatterns')
            ->with($patterns);
        $mock->expects($this->once())
            ->method('run')
            ->will($this->returnValue(true));
        $c = new Scisr_CLI();
        $c->setScisr($mock);
        $c->process($args);
    }

    public function ignoreOptProvider() {
        return array(
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '--ignore', 'foo', 'file1.php'), array('foo')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '--ignore', 'dir/foo,another/dir/,baz', 'file1.php'), array('dir/foo', 'another/dir/', 'baz')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '--ignore=foo,bar,baz', 'file1.php'), array('foo', 'bar', 'baz')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '-i', 'foo,bar,baz', 'file1.php'), array('foo', 'bar', 'baz')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '-ifoo,bar,baz', 'file1.php'), array('foo', 'bar', 'baz')),
        );
    }

    /**
     * @dataProvider extensionsOptProvider
     */
    public function testSetExtensions($args, $patterns) {
        $mock = $this->getMock('Scisr');
        $mock->expects($this->once())
            ->method('setRenameClass')
            ->with($this->equalTo('Foo'), $this->equalTo('Baz'));
        $mock->expects($this->once())
            ->method('setAllowedFileExtensions')
            ->with($patterns);
        $mock->expects($this->once())
            ->method('run')
            ->will($this->returnValue(true));
        $c = new Scisr_CLI();
        $c->setScisr($mock);
        $c->process($args);
    }

    public function extensionsOptProvider() {
        return array(
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '--extensions', 'foo', 'file1.php'), array('foo')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '--extensions', 'html,php,inc,phtml', 'file1.php'), array('html', 'php', 'inc', 'phtml')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '--extensions=html,php,inc,phtml', 'file1.php'), array('html', 'php', 'inc', 'phtml')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '-e', 'foo,bar,baz', 'file1.php'), array('foo', 'bar', 'baz')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '-efoo,bar,baz', 'file1.php'), array('foo', 'bar', 'baz')),
        );
    }

    /**
     * @dataProvider aggressiveOptProvider
     */
    public function testSetAggressive($args) {
        $mock = $this->getMock('Scisr');
        $mock->expects($this->once())
            ->method('setRenameClass')
            ->with($this->equalTo('Foo'), $this->equalTo('Baz'));
        $mock->expects($this->once())
            ->method('setEditMode')
            ->with($this->equalTo(Scisr::MODE_AGGRESSIVE));
        $mock->expects($this->once())
            ->method('run')
            ->will($this->returnValue(true));
        $c = new Scisr_CLI();
        $c->setScisr($mock);
        $c->process($args);
    }

    public function aggressiveOptProvider() {
        return array(
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '-a', 'file1.php', 'file2.php')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '--aggressive', 'file1.php', 'file2.php')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', 'file1.php', '-a', 'file2.php')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', 'file1.php', 'file2.php', '--aggressive')),
        );
    }

    /**
     * @dataProvider timidOptProvider
     */
    public function testSetTimid($args) {
        $mock = $this->getMock('Scisr');
        $mock->expects($this->once())
            ->method('setRenameClass')
            ->with($this->equalTo('Foo'), $this->equalTo('Baz'));
        $mock->expects($this->once())
            ->method('setEditMode')
            ->with($this->equalTo(Scisr::MODE_TIMID));
        $mock->expects($this->once())
            ->method('run')
            ->will($this->returnValue(true));
        $c = new Scisr_CLI();
        $c->setScisr($mock);
        $c->process($args);
    }

    public function timidOptProvider() {
        return array(
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '-t', 'file1.php', 'file2.php')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '--timid', 'file1.php', 'file2.php')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', 'file1.php', '-t', 'file2.php')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', 'file1.php', 'file2.php', '--timid')),
        );
    }

    /**
     * @dataProvider nonValueArgsProvider
     */
    public function testDontAllowValuesToNonValueArg($args) {
        $stub = $this->getMock('Scisr');
        $output = new Scisr_CaptureOutput();
        $c = new Scisr_CLI($output);
        $c->setScisr($stub);
        $this->assertNotEquals(0, $c->process($args));
        // Let's make sure it printed a usage message too
        $this->assertRegExp('/error.*does not accept a value/i', $output->getOutput());
    }

    public function nonValueArgsProvider() {
        return array(
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '--timid=foo', 'file1.php')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '--aggressive=foo,bar/stuff', 'file1.php')),
            array(array('scisr_executable', 'rename-class', 'Foo', 'Baz', '-tfoo', 'foo', 'file1.php')),
        );
    }

    public function testDontAllowTimidAndAggressiveAtSameTime() {
        $this->markTestSkipped("Maybe it's okay if both are passed, and we just accept the last one?");
        $stub = $this->getMock('Scisr');
        $args = array('scisr_executable', 'rename-class', 'Foo', 'Baz', '--timid', '--aggressive', 'file1.php');
        $c = new Scisr_CLI();
        $c->setScisr($stub);
        $this->setExpectedException('Exception');
        $c->process($args);
    }

    public function testGiveMultipleFiles() {
        $mock = $this->getMock('Scisr');
        $mock->expects($this->once())
            ->method('setRenameClass')
            ->with($this->equalTo('Foo'), $this->equalTo('Baz'));
        $mock->expects($this->once())
            ->method('addFiles')
            ->with($this->equalTo(array('somefile.php', 'some/other/file.foo', 'someDirectory')));
        $mock->expects($this->once())
            ->method('run')
            ->will($this->returnValue(true));
        $args = array('scisr_executable', 'rename-class', 'Foo', 'Baz', 'somefile.php', 'some/other/file.foo', 'someDirectory');
        $c = new Scisr_CLI();
        $c->setScisr($mock);
        $c->process($args);
    }

    public function testRenameClass() {
        $mock = $this->getMock('Scisr');
        $mock->expects($this->once())
            ->method('setRenameClass')
            ->with($this->equalTo('Foo'), $this->equalTo('Baz'));
        $mock->expects($this->once())
            ->method('addFiles')
            ->with($this->equalTo(array('somefile.php')));
        $mock->expects($this->once())
            ->method('run')
            ->will($this->returnValue(true));
        $args = array('scisr_executable', 'rename-class', 'Foo', 'Baz', 'somefile.php');
        $c = new Scisr_CLI();
        $c->setScisr($mock);
        $c->process($args);
    }

    public function testRenameMethod() {
        $mock = $this->getMock('Scisr');
        $mock->expects($this->once())
            ->method('setRenameMethod')
            ->with($this->equalTo('Foo'), $this->equalTo('bar'), $this->equalTo('baz'));
        $mock->expects($this->once())
            ->method('addFiles')
            ->with($this->equalTo(array('somefile.php')));
        $mock->expects($this->once())
            ->method('run')
            ->will($this->returnValue(true));
        $args = array('scisr_executable', 'rename-method', 'Foo', 'bar', 'baz', 'somefile.php');
        $c = new Scisr_CLI();
        $c->setScisr($mock);
        $c->process($args);
    }

    public function testRenameFile() {
        $mock = $this->getMock('Scisr');
        $mock->expects($this->once())
            ->method('setRenameFile')
            ->with($this->equalTo('mydir/foo.php'), $this->equalTo('mydir/newdir/bar.php'));
        $mock->expects($this->once())
            ->method('addFiles')
            ->with($this->equalTo(array('mydir')));
        $mock->expects($this->once())
            ->method('run')
            ->will($this->returnValue(true));
        $args = array('scisr_executable', 'rename-file', 'mydir/foo.php', 'mydir/newdir/bar.php', 'mydir');
        $c = new Scisr_CLI();
        $c->setScisr($mock);
        $c->process($args);
    }

    /**
     * @dataProvider badArgsProvider
     */
    public function testDontRunOnBadArgs($args) {
        array_unshift($args, 'scisr_executable');
        $mock = $this->getMock('Scisr');
        $mock->expects($this->never())
            ->method('run')
            ->will($this->returnValue(true));
        $output = new Scisr_CaptureOutput();
        $c = new Scisr_CLI($output);
        $c->setScisr($mock);
        $c->process($args);
        // Let's make sure it printed a usage message too
        $this->assertRegExp('/usage/i', $output->getOutput());
    }

    public function badArgsProvider() {
        return array(
            array(array('foo')),
            array(array('foo', 'bar', 'baz', 'file.php')),
            array(array('rename-class', 'bar')),
            array(array('rename-class', 'bar', 'baz')),
            array(array('rename-file', 'file.php')),
            array(array('rename-file')),
            array(array('rename-method', 'bar', 'baz', 'file.php')),
            array(array('rename-class', '--unrecognized', 'baz', 'file.php')),
            array(array('rename-class', 'bar', '-z', 'file.php')),
        );
    }

}
