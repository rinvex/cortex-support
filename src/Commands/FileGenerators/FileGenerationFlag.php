<?php

declare(strict_types=1);

namespace Cortex\Support\Commands\FileGenerators;

class FileGenerationFlag
{
    const EMBEDDED_PANEL_RESOURCE_SCHEMAS = 'embedded_panel_resource_schemas';

    const EMBEDDED_PANEL_RESOURCE_TABLES = 'embedded_panel_resource_tables';

    const PARTIAL_IMPORTS = 'partial_imports';

    const PANEL_CLUSTER_CLASSES_OUTSIDE_DIRECTORIES = 'panel_cluster_classes_outside_directories';

    const PANEL_RESOURCE_CLASSES_OUTSIDE_DIRECTORIES = 'panel_resource_classes_outside_directories';
}
