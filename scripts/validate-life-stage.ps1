<#
.SYNOPSIS
Structural validation of the Life Stage layer (puppy/adult/senior) in the theme.

.DESCRIPTION
No live WordPress needed. Asserts the source contains the load-bearing pieces of
the life-stage layer so regressions are caught on commit:

  1. `life_stage` taxonomy registered on `post` (non-hierarchical, show_in_rest,
     rest_base 'life_stage').
  2. Terms puppy/adult/senior enumerated by `tailwell_life_stages()`.
  3. Query var + `pre_get_posts` filter for archive filtering.
  4. `[tw_article_list]` and `[tw_related_articles]` accept a `life_stage`
     attribute and pass a `life_stage` tax_query.
  5. single.php renders the life-stage badge next to the category.
  6. Archive pages get the 3-pill filter (never on senior-special-needs).
  7. style.css ships `.tw-badge`, `.tw-badge--life-stage`, `.tw-life-stage-filter`.

Run with no arguments; exit code 0 = PASS, 1 = FAIL.
#>

$ErrorActionPreference = 'Stop'
$root = Split-Path $PSScriptRoot -Parent   # repo root

$files = @{
    'functions.php'   = Join-Path $root 'wordpress\tailwell-theme\functions.php'
    'shortcodes.php'  = Join-Path $root 'wordpress\tailwell-theme\inc\shortcodes.php'
    'single.php'      = Join-Path $root 'wordpress\tailwell-theme\single.php'
    'style.css'       = Join-Path $root 'wordpress\tailwell-theme\style.css'
}

$failed = $false

function Assert-Contains {
    param(
        [string]$FileKey,
        [string]$Pattern,
        [string]$What
    )
    $content = Get-Content -LiteralPath $files[$FileKey] -Raw
    if ($content -match $Pattern) {
        Write-Output ("[PASS] {0} : {1}" -f $FileKey, $What)
    }
    else {
        $script:failed = $true
        Write-Output ("[FAIL] {0} : {1}" -f $FileKey, $What)
    }
}

Write-Output 'TailWell Life Stage layer — structural validation'

Assert-Contains 'functions.php' "register_taxonomy\(\s*'life_stage'" 'taxonomy registered'
Assert-Contains 'functions.php' "'hierarchical'\s*=>\s*false" 'non-hierarchical (tag-style)'
Assert-Contains 'functions.php' "'show_in_rest'\s*=>\s*true" 'exposed via REST'
Assert-Contains 'functions.php' "'rest_base'\s*=>\s*'life_stage'" "REST base 'life_stage'"
Assert-Contains 'functions.php' "(?s)puppy.*adult.*senior" 'terms puppy/adult/senior'
Assert-Contains 'functions.php' "add_filter\(\s*'query_vars'" 'life_stage query var registered'
Assert-Contains 'functions.php' "add_action\(\s*'pre_get_posts'" 'archive filter hooked'

Assert-Contains 'shortcodes.php' "add_shortcode\(\s*'tw_article_list'" '[tw_article_list] registered'
Assert-Contains 'shortcodes.php' "add_shortcode\(\s*'tw_related_articles'" '[tw_related_articles] registered'
Assert-Contains 'shortcodes.php' "'life_stage'\s*=>\s*''" 'life_stage attribute on both shortcodes'
Assert-Contains 'shortcodes.php' "'taxonomy'\s*=>\s*'life_stage'" 'life_stage tax_query in article queries'
Assert-Contains 'shortcodes.php' "tailwell_life_stage_filter" 'archive filter function exists'
Assert-Contains 'shortcodes.php' $("'senior-special-needs'" + '\s*===\s*' + [regex]::Escape('$slug')) 'senior silo excluded from pill filter'

Assert-Contains 'single.php' 'tailwell_life_stage_badge_html' 'life-stage badge on article template'

Assert-Contains 'style.css' '\.tw-badge' 'badge pill CSS'
Assert-Contains 'style.css' '\.tw-badge--life-stage' 'life-stage badge modifier CSS'
Assert-Contains 'style.css' '\.tw-life-stage-filter' 'archive pill row CSS'

Write-Output ""
if ($failed) {
    Write-Output 'Life Stage layer validation FAILED'
    exit 1
}
Write-Output 'Life Stage layer validation OK'
exit 0