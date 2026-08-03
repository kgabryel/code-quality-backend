<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Enum\Text;

enum Label: string
{
    case AnalysisTools = 'Analysis tools';
    case ComposerTools = 'Composer tools';
    case ContainerName = 'Container name';
    case Continue = 'Continue anyway?';
    case ContinueWithoutDependencies = 'Continue anyway and generate configs only?';
    case Framework = 'Framework';
    case GenerateScripts = 'Generate helper scripts?';
    case InstallDependencies = 'Install dependencies with Composer?';
    case PHPVersion = 'PHP version';
    case ScriptEnvironment = 'Script execution environment';
    case Scripts = 'Helper scripts';
    case TestTools = 'Test tools';
}
