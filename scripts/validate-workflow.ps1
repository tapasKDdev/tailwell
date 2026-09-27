param(
    [string]$WorkflowPath = (Join-Path (Join-Path (Join-Path $PSScriptRoot '..') 'n8n') 'tailwell-end-to-end.json')
)

$ErrorActionPreference = 'Stop'
$resolved = (Resolve-Path $WorkflowPath).Path

$script = @'
const fs = require('fs');
const wf = JSON.parse(fs.readFileSync(process.argv[2], 'utf8'));
const names = wf.nodes.map(n => n.name);
const dup = names.filter((n, i) => names.indexOf(n) !== i);
const targets = [];
for (const [from, conns] of Object.entries(wf.connections)) {
  (conns.main || []).forEach(list => list.forEach(ref => targets.push({ from, to: ref.node })));
}
const missing = targets.filter(t => !names.includes(t.to));
const codeResults = wf.nodes.filter(n => n.parameters && n.parameters.jsCode).map(n => {
  try { new Function(n.parameters.jsCode); return { name: n.name, ok: true }; }
  catch (e) { return { name: n.name, ok: false, error: e.message }; }
});
const report = { name: wf.name, nodeCount: wf.nodes.length, duplicateNames: dup, brokenConnectionTargets: missing, codeNodes: codeResults };
console.log(JSON.stringify(report, null, 1));
if (dup.length || missing.length || codeResults.some(c => !c.ok)) process.exit(1);
'@

$tmpJs = Join-Path $env:TEMP ("wf-check-" + [guid]::NewGuid().ToString() + ".js")
try {
    [System.IO.File]::WriteAllText($tmpJs, $script, (New-Object System.Text.UTF8Encoding($false)))
    & node $tmpJs $resolved
    if ($LASTEXITCODE -ne 0) {
        Write-Output "Workflow validation FAILED: $resolved"
        exit 1
    }
    Write-Output "Workflow validation OK: $resolved"
} finally {
    Remove-Item -LiteralPath $tmpJs -ErrorAction SilentlyContinue
}