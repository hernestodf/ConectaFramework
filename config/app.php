<?php

return [
    // =============================================
    // IDENTIDADE DO SISTEMA
    // =============================================
    'name' => 'ConectaFramework',
    'title' => 'ConectaFramework - Painel',
    'version' => '1.0.0',
    
    // Logo como imagem (PNG) - deixe vazio para usar logo_text
    'logo' => '',  // Ex: '/assets/logo.png'
    
    // Texto do logo se não tiver imagem (fallback)
    'logo_text' => 'C',
    
    // Nome da empresa (para display)
    'company' => 'ConectaFramework',
    
    // =============================================
    // TEMA DO SISTEMA
    // =============================================
    'theme' => [
        // Tema ativo: default | pink | blue | green | dark
        'active' => 'default',
        
        // Temas disponíveis
        'themes' => [
            'default' => [
                'name' => 'Cyan (Padrão)',
                'primary' => '#0B6E8C',
                'primary_glow' => 'rgba(11,110,140,0.28)',
                'sidebar_bg' => '#0F172A',
                'sidebar_hover' => 'rgba(255,255,255,0.1)',
                'sidebar_text' => 'rgba(255,255,255,0.7)',
                'surface' => '#E0E8F2',
                'background' => '#EDF1F7',
                'text' => '#1E2E45',
                'text_light' => '#4A6080',
            ],
            
            'pink' => [
                'name' => 'Rosa Chamativo',
                'primary' => '#E11D48',
                'primary_glow' => 'rgba(225,29,72,0.28)',
                'sidebar_bg' => '#1E1B4B',
                'sidebar_hover' => 'rgba(225,29,72,0.4)',
                'sidebar_text' => 'rgba(255,255,255,0.75)',
                'surface' => '#FCE7F3',
                'background' => '#FDF2F8',
                'text' => '#1E1B4B',
                'text_light' => '#4C1D4E',
            ],
            
            'blue' => [
                'name' => 'Blue Professional',
                'primary' => '#1D4ED8',
                'primary_glow' => 'rgba(29,78,216,0.28)',
                'sidebar_bg' => '#1E3A5F',
                'sidebar_hover' => 'rgba(29,78,216,0.4)',
                'sidebar_text' => 'rgba(255,255,255,0.75)',
                'surface' => '#DBEAFE',
                'background' => '#EFF6FF',
                'text' => '#1E3A8A',
                'text_light' => '#3B82F6',
            ],
            
            'green' => [
                'name' => 'Green Nature',
                'primary' => '#059669',
                'primary_glow' => 'rgba(5,150,105,0.28)',
                'sidebar_bg' => '#064E3B',
                'sidebar_hover' => 'rgba(5,150,105,0.4)',
                'sidebar_text' => 'rgba(255,255,255,0.75)',
                'surface' => '#D1FAE5',
                'background' => '#ECFDF5',
                'text' => '#064E3B',
                'text_light' => '#059669',
            ],
            
            'dark' => [
                'name' => 'Dark Mode',
                'primary' => '#8B5CF6',
                'primary_glow' => 'rgba(139,92,246,0.28)',
                'sidebar_bg' => '#0F0F0F',
                'sidebar_hover' => 'rgba(255,255,255,0.1)',
                'sidebar_text' => 'rgba(255,255,255,0.7)',
                'surface' => '#1A1A1A',
                'background' => '#0A0A0A',
                'text' => '#E5E5E5',
                'text_light' => '#A3A3A3',
            ],
        ],
    ],
    
    // =============================================
    // OUTRAS CONFIGURAÇÕES
    // =============================================
    'timezone' => 'America/Sao_Paulo',
    'session_lifetime' => 120,
];