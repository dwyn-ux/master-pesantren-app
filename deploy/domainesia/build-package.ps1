param(
    [string] $OutputPath = "deploy/domainesia/master-pesantren-app-domainesia.zip",
    [switch] $IncludeVendor
)

$ErrorActionPreference = "Stop"

$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot "../..")).Path
$outputFullPath = if ([System.IO.Path]::IsPathRooted($OutputPath)) {
    $OutputPath
} else {
    Join-Path $projectRoot $OutputPath
}

$outputDirectory = Split-Path -Parent $outputFullPath
if (-not (Test-Path $outputDirectory)) {
    New-Item -ItemType Directory -Path $outputDirectory | Out-Null
}

$stagingRoot = Join-Path ([System.IO.Path]::GetTempPath()) ("domainesia-package-" + [System.Guid]::NewGuid().ToString("N"))
$stagingApp = Join-Path $stagingRoot "master-pesantren-app"

$excludedDirectories = @(
    ".git",
    ".git.backup_pesantren-app",
    ".idea",
    ".vscode",
    ".codex",
    ".cursor",
    ".opencode",
    "node_modules",
    "vendor",
    "public/storage",
    "storage/app/mpdf-tmp",
    "storage/framework/cache",
    "storage/framework/sessions",
    "storage/framework/testing",
    "storage/framework/views",
    "storage/logs"
) | ForEach-Object { Join-Path $projectRoot $_ }

if ($IncludeVendor) {
    $vendorPath = Join-Path $projectRoot "vendor"
    $excludedDirectories = $excludedDirectories | Where-Object {
        -not $_.Equals($vendorPath, [System.StringComparison]::OrdinalIgnoreCase)
    }
}

$excludedFiles = @(
    ".env",
    ".env.backup",
    ".env.production",
    ".phpunit.result.cache",
    "auth.json",
    "Akun Ustadz.txt",
    "query",
    "credentials-*.txt",
    "*.pem",
    "*.key",
    "id_rsa",
    "id_rsa.pub",
    "known_hosts",
    "authorized_keys"
)

try {
    New-Item -ItemType Directory -Path $stagingApp | Out-Null

    $excludedDirectoryPaths = $excludedDirectories | ForEach-Object {
        [System.IO.Path]::GetFullPath($_).TrimEnd([System.IO.Path]::DirectorySeparatorChar)
    }

    function Test-IsExcludedDirectory {
        param([string] $Path)

        $fullPath = [System.IO.Path]::GetFullPath($Path).TrimEnd([System.IO.Path]::DirectorySeparatorChar)
        foreach ($excludedPath in $excludedDirectoryPaths) {
            if ($fullPath.Equals($excludedPath, [System.StringComparison]::OrdinalIgnoreCase) -or
                $fullPath.StartsWith($excludedPath + [System.IO.Path]::DirectorySeparatorChar, [System.StringComparison]::OrdinalIgnoreCase)) {
                return $true
            }
        }

        return $false
    }

    function Test-IsExcludedFile {
        param([string] $Name)

        foreach ($pattern in $excludedFiles) {
            if ($Name -like $pattern) {
                return $true
            }
        }

        return $false
    }

    $stack = [System.Collections.Generic.Stack[string]]::new()
    $stack.Push($projectRoot)

    while ($stack.Count -gt 0) {
        $currentDirectory = $stack.Pop()
        if (Test-IsExcludedDirectory $currentDirectory) {
            continue
        }

        $relativeDirectory = [System.IO.Path]::GetRelativePath($projectRoot, $currentDirectory)
        $targetDirectory = if ($relativeDirectory -eq ".") {
            $stagingApp
        } else {
            Join-Path $stagingApp $relativeDirectory
        }

        if (-not (Test-Path $targetDirectory)) {
            New-Item -ItemType Directory -Path $targetDirectory | Out-Null
        }

        foreach ($item in Get-ChildItem -LiteralPath $currentDirectory -Force) {
            if (($item.Attributes -band [System.IO.FileAttributes]::ReparsePoint) -ne 0) {
                continue
            }

            if ($item.PSIsContainer) {
                if (-not (Test-IsExcludedDirectory $item.FullName)) {
                    $stack.Push($item.FullName)
                }
                continue
            }

            if (Test-IsExcludedFile $item.Name) {
                continue
            }

            $relativeFile = [System.IO.Path]::GetRelativePath($projectRoot, $item.FullName)
            $targetFile = Join-Path $stagingApp $relativeFile
            $targetParent = Split-Path -Parent $targetFile

            if (-not (Test-Path $targetParent)) {
                New-Item -ItemType Directory -Path $targetParent | Out-Null
            }

            Copy-Item -LiteralPath $item.FullName -Destination $targetFile -Force
        }
    }

    $runtimeDirectories = @(
        "storage/app/public",
        "storage/app/private",
        "storage/framework/cache/data",
        "storage/framework/sessions",
        "storage/framework/views",
        "storage/logs",
        "bootstrap/cache"
    )

    foreach ($directory in $runtimeDirectories) {
        $fullDirectory = Join-Path $stagingApp $directory
        if (-not (Test-Path $fullDirectory)) {
            New-Item -ItemType Directory -Path $fullDirectory | Out-Null
        }
    }

    $keepFiles = @(
        "storage/app/public/.gitkeep",
        "storage/app/private/.gitkeep",
        "storage/framework/cache/data/.gitkeep",
        "storage/framework/sessions/.gitkeep",
        "storage/framework/views/.gitkeep",
        "storage/logs/.gitkeep",
        "bootstrap/cache/.gitkeep"
    )

    foreach ($file in $keepFiles) {
        $fullFile = Join-Path $stagingApp $file
        if (-not (Test-Path $fullFile)) {
            New-Item -ItemType File -Path $fullFile -Force | Out-Null
        }
    }

    if (Test-Path $outputFullPath) {
        Remove-Item -LiteralPath $outputFullPath -Force
    }

    Add-Type -AssemblyName System.IO.Compression.FileSystem
    [System.IO.Compression.ZipFile]::CreateFromDirectory(
        $stagingRoot,
        $outputFullPath,
        [System.IO.Compression.CompressionLevel]::Optimal,
        $false
    )

    Write-Host "Package ready: $outputFullPath"
} finally {
    if (Test-Path $stagingRoot) {
        Remove-Item -LiteralPath $stagingRoot -Recurse -Force
    }
}
