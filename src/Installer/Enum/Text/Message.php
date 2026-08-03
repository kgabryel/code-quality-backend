<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Enum\Text;

enum Message: string
{
    case Aborted = 'Installation aborted';
    case ComposerFailed = 'Dependency installation failed';
    case ComposerNotFound = 'Composer not found';
    case ConfigsGenerating = 'Generating configuration...';
    case ContainerNameMissing = 'Container name is required for the Docker environment';
    case DependenciesInstalled = 'Dependencies installed';
    case DependenciesInstalling = 'Installing dependencies...';
    case DependenciesSkipped = 'Dependency installation skipped. Install them manually:';
    case Done = 'Done';
    case FileCreated = 'Created %s';
    case FileExists = 'File %s already exists. Overwrite?';
    case FileSkipped = 'Skipped %s';
    case FileUpdated = 'Updated %s';
    case GitignoreHint = 'Add to .gitignore:';
    case InstallHint = 'No → only generates configs, install the packages yourself';
    case InvalidCommand = 'Invalid command';
    case InvalidContainerName = 'Invalid container name';
    case InvalidPHPVersion = 'Unsupported PHP version';
    case MultiselectHint = 'Space to toggle, Enter to confirm';
    case NoComposerJson = 'There is no composer.json';
    case NoCoverageDriver = 'No coverage driver detected (Xdebug or PCOV)';
    case NoPackages = 'No packages to install';
    case NothingSelected = 'No tools selected — there is nothing to install or configure';
    case PackageRootUnavailable = 'Unable to determine the package installation directory';
    case ProjectRootError = 'Unable to determine the project directory';
    case ScriptsGenerating = 'Generating helper scripts...';
    case SelectHint = 'Arrows ↑↓ to navigate, Enter to confirm';
    case TemplateUnreadable = 'Unable to read template: %s';
    case UnexpectedError = 'Unexpected error: %s';
    case WriteFailed = 'Unable to write file %s';
}
