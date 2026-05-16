$stageFiles = @(
    'stage-1-tunisia.html',
    'stage-2-maghreb.html',
    'stage-3-africa.html',
    'stage-4-europe.html',
    'stage-5-asia.html',
    'stage-6-namerica.html',
    'stage-7-samerica.html',
    'stage-8-oceania.html',
    'stage-9-poles.html',
    'stage-10-world.html'
)

Write-Host '📊 VERIFICATION COMPLETE DU SYSTÈME' -ForegroundColor Yellow
Write-Host '===================================' -ForegroundColor Yellow
Write-Host ''

foreach($file in $stageFiles) {
    $path = "c:\xampppp\htdocs\monde-magique\" + $file
    if (Test-Path $path) {
        $content = Get-Content $path -Raw
        
        $hasQCMQuestions = $content -match 'السؤال 1:'
        $hasProgressSave = $content -match 'saveProgress'
        $has80Percent = $content -match 'gameState.qcmScore<80'
        
        $fname = $file.Replace('.html', '')
        $status = if ($hasQCMQuestions -and $hasProgressSave -and $has80Percent) { 'OK' } else { 'ERREUR' }
        
        Write-Host "$fname`t$status" -ForegroundColor Cyan
        
        if (-not $hasQCMQuestions) { Write-Host '  Warning: QCM not found' -ForegroundColor Red }
        if (-not $hasProgressSave) { Write-Host '  Warning: Progress save not found' -ForegroundColor Red }
        if (-not $has80Percent) { Write-Host '  Warning: 80% check not found' -ForegroundColor Red }
    }
}

Write-Host ''
Write-Host 'VERIFICATION COMPLETE' -ForegroundColor Green
