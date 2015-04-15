<?php

/**
 * RequireJS AMD
 */
 
class BitrixAdapterJS
{
    private $config, $loads   = array(), $enabled = array(), $scripts = array();
    public function __construct()
    {
    $this->config = array(
        'base_url'  => (getenv('HIVE_ENV') == 'DEVEL') ? '/js-dev/lib' : '/js/lib',
        'requirejs' => '/js/require-jquery.js',
        'prefix'    => '../app/'
        );
    }
    
    public function config(array $config)
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    public function load()
    {
        $loads = func_get_args();
        $this->loads = array_unique(array_merge($this->loads, $loads));
        return $this;
    }

    public function enable()
    {
        $enabled = func_get_args();
        $this->enabled = array_unique(array_merge($this->enabled, $enabled));
        return $this;
    }

    public function begin()
    {
        ob_start();
        return $this;
    }

    public function end()
    {
        $this->scripts[] = ob_get_contents();
        ob_end_clean();
        return $this;
    }

    public function script($script)
    {
        $this->scripts[] = $script;
        return $this;
    }

    private function mklist($items, $prefix = '', $quoted = true)
    {
        return implode(
            ', ',
            array_map(
                create_function(
                    '$x', 
                    $quoted ? 
                        'return "\''.$prefix.'$x\'";' : 
                        'return "'.$prefix.'$x";'),
                $items));
    }

    public function dump()
    {
?>
<script>
  var require = {
    baseUrl: '<?php print $this->config['base_url'] ?>'
    <?php if (getenv('HIVE_ENV') == 'DEVEL') { ?>,urlArgs: "bust=v<?php echo date('dmYs'); ?>"<?php } ?>
  };
</script>
<script src="<?php print $this->config['requirejs'] ?>"></script>
<script>
require([<?php print $this->mklist($this->loads, $this->config['prefix']); ?>], function() {
require([<?php print $this->mklist($this->enabled) ?>], function() {
<?php foreach ($this->scripts as $s) { ?>
  <?php echo $s; ?>
<?php } ?>
});
});
</script>
<?php
    return $this;
    }
}
