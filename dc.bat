# Add to your PowerShell profile
# To edit profile: notepad $PROFILE

function dc {
    param(
        [Parameter(Position=0, ValueFromRemainingArguments=$true)]
        [string[]]$Arguments
    )
    
    if ($Arguments -contains "up") {
        # Get IP address
        $hostIP = Get-NetIPAddress -AddressFamily IPv4 | 
                  Where-Object { 
                      $_.InterfaceAlias -notlike '*Loopback*' -and 
                      $_.IPAddress -notlike '169.254.*' 
                  } | 
                  Select-Object -First 1 -ExpandProperty IPAddress
        
        Write-Host "Using IP: $hostIP" -ForegroundColor Green
        
        # Set environment variable
        $env:HOST_IP = $hostIP
        
        # Create .env file
        "HOST_IP=$hostIP" | Out-File -FilePath ".env" -Encoding ASCII
    }
    
    # Run docker-compose
    & docker-compose $Arguments
    
    if ($Arguments -contains "up" -and $Arguments -contains "-d") {
        Write-Host "`nAccess at: https://app.$env:HOST_IP.nip.io" -ForegroundColor Cyan
    }
}

# Optional: Alias docker-compose to dc
Set-Alias -Name docker-compose -Value dc