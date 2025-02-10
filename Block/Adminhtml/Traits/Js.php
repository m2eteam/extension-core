<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Traits;

trait Js
{
    private function prepareOnReadyJs(string $jsCode): string
    {
        return
            <<<JS
<script type="text/javascript">
require(["jquery"], function(jQuery) {
        jQuery(function() {{$jsCode}});
    });
</script>
JS;
    }

    private function prepareRequireJs(array $dependencies, $script): string
    {
        $parameters = array_keys($dependencies);
        $modules = array_values($dependencies);

        $preparedParameters = implode(',', $parameters);
        $preparedModules = implode('","', $modules);

        return <<<JS
<script type="text/javascript">
require(["$preparedModules"], function($preparedParameters) {
    {$script}
})
</script>
JS;
    }
}
