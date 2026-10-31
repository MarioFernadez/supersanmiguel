<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\StoreSetting;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SifenService
{
    /**
     * Determina si el Master ha habilitado el módulo de Facturación Electrónica en la licencia del negocio.
     */
    public static function isMasterEnabled(): bool
    {
        $setting = StoreSetting::where('key', 'sifen_master_enabled')->first();
        return $setting ? ($setting->value === '1' || $setting->value === 1) : true;
    }

    /**
     * Verifica si la Facturación Electrónica (SIFEN) está activada y habilitada por el Master.
     */
    public static function isEnabled(): bool
    {
        if (!self::isMasterEnabled()) {
            return false;
        }
        $setting = StoreSetting::where('key', 'sifen_enabled')->first();
        return $setting ? ($setting->value === '1' || $setting->value === 1 || $setting->value === 'true') : false;
    }

    /**
     * Retorna el ambiente configurado: 'test' (Homologación/Pruebas) o 'production'.
     */
    public static function getEnvironment(): string
    {
        return StoreSetting::where('key', 'sifen_environment')->value('value') ?: 'test';
    }

    /**
     * Indica si se está operando en ambiente de prueba.
     */
    public static function isTestMode(): bool
    {
        return self::getEnvironment() === 'test';
    }

    /**
     * Obtiene todas las configuraciones SIFEN con valores predeterminados seguros.
     */
    public static function getSettings(): array
    {
        $keys = [
            'sifen_enabled',
            'sifen_environment',
            'sifen_timbrado',
            'sifen_timbrado_fecha_inicio',
            'sifen_establecimiento',
            'sifen_punto_expedicion',
            'sifen_serie',
            'sifen_ultimo_numero',
            'sifen_emisor_ruc',
            'sifen_emisor_dv',
            'sifen_emisor_tipo_contribuyente',
            'sifen_emisor_razon_social',
            'sifen_emisor_nombre_fantasia',
            'sifen_emisor_direccion',
            'sifen_emisor_numero_casa',
            'sifen_emisor_departamento',
            'sifen_emisor_departamento_desc',
            'sifen_emisor_distrito',
            'sifen_emisor_distrito_desc',
            'sifen_emisor_ciudad',
            'sifen_emisor_ciudad_desc',
            'sifen_emisor_telefono',
            'sifen_emisor_email',
            'sifen_emisor_actividad_codigo',
            'sifen_emisor_actividad_desc',
            'sifen_id_csc',
            'sifen_csc',
            'sifen_cert_path',
            'sifen_cert_password',
            'sifen_auto_print_kude',
        ];

        $raw = StoreSetting::whereIn('key', $keys)->pluck('value', 'key')->toArray();
        $isTest = ($raw['sifen_environment'] ?? 'test') === 'test';

        return [
            'sifen_enabled'                  => isset($raw['sifen_enabled']) ? ($raw['sifen_enabled'] === '1') : false,
            'sifen_environment'              => $raw['sifen_environment'] ?? 'test',
            'sifen_timbrado'                 => $raw['sifen_timbrado'] ?? '12345678',
            'sifen_timbrado_fecha_inicio'    => $raw['sifen_timbrado_fecha_inicio'] ?? '2024-01-01',
            'sifen_establecimiento'          => $raw['sifen_establecimiento'] ?? '001',
            'sifen_punto_expedicion'         => $raw['sifen_punto_expedicion'] ?? '001',
            'sifen_serie'                    => $raw['sifen_serie'] ?? 'AA',
            'sifen_ultimo_numero'            => (int) ($raw['sifen_ultimo_numero'] ?? 0),
            'sifen_emisor_ruc'               => $raw['sifen_emisor_ruc'] ?? '44444401',
            'sifen_emisor_dv'                => $raw['sifen_emisor_dv'] ?? '7',
            'sifen_emisor_tipo_contribuyente'=> $raw['sifen_emisor_tipo_contribuyente'] ?? '2',
            'sifen_emisor_razon_social'      => $raw['sifen_emisor_razon_social'] ?? ($isTest ? 'DOCUMENTO ELECTRÓNICO SIN VALOR COMERCIAL NI FISCAL - GENERADO EN AMBIENTE DE PRUEBA' : 'ZONASHOP COMERCIAL S.A.'),
            'sifen_emisor_nombre_fantasia'   => $raw['sifen_emisor_nombre_fantasia'] ?? 'ZonaShop Supermercado',
            'sifen_emisor_direccion'         => $raw['sifen_emisor_direccion'] ?? 'Avda. Eusebio Ayala 1234',
            'sifen_emisor_numero_casa'       => $raw['sifen_emisor_numero_casa'] ?? '0',
            'sifen_emisor_departamento'      => $raw['sifen_emisor_departamento'] ?? '11',
            'sifen_emisor_departamento_desc' => $raw['sifen_emisor_departamento_desc'] ?? 'Capital',
            'sifen_emisor_distrito'          => $raw['sifen_emisor_distrito'] ?? '1',
            'sifen_emisor_distrito_desc'     => $raw['sifen_emisor_distrito_desc'] ?? 'Asunción',
            'sifen_emisor_ciudad'            => $raw['sifen_emisor_ciudad'] ?? '1',
            'sifen_emisor_ciudad_desc'       => $raw['sifen_emisor_ciudad_desc'] ?? 'Asunción',
            'sifen_emisor_telefono'          => $raw['sifen_emisor_telefono'] ?? '021123456',
            'sifen_emisor_email'             => $raw['sifen_emisor_email'] ?? 'facturacion@zonapos.com',
            'sifen_emisor_actividad_codigo'  => $raw['sifen_emisor_actividad_codigo'] ?? '47111',
            'sifen_emisor_actividad_desc'    => $raw['sifen_emisor_actividad_desc'] ?? 'Comercio al por menor en comercios no especializados',
            'sifen_id_csc'                   => $raw['sifen_id_csc'] ?? '0001',
            'sifen_csc'                      => $raw['sifen_csc'] ?? 'ABCD0000000000000000000000000000',
            'sifen_cert_path'                => $raw['sifen_cert_path'] ?? '',
            'sifen_cert_password'            => $raw['sifen_cert_password'] ?? '',
            'sifen_auto_print_kude'          => isset($raw['sifen_auto_print_kude']) ? ($raw['sifen_auto_print_kude'] === '1') : true,
        ];
    }

    /**
     * Guarda o actualiza un array de claves en StoreSetting.
     */
    public static function saveSettings(array $settings): void
    {
        foreach ($settings as $key => $value) {
            StoreSetting::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
            );
        }
    }

    /**
     * Carga los parámetros oficiales de prueba estipulados por la DNIT en la Guía de Pruebas.
     */
    public static function loadDefaultTestData(): void
    {
        $defaults = [
            'sifen_enabled'                  => '1',
            'sifen_environment'              => 'test',
            'sifen_timbrado'                 => '12345678',
            'sifen_timbrado_fecha_inicio'    => '2024-01-01',
            'sifen_establecimiento'          => '001',
            'sifen_punto_expedicion'         => '001',
            'sifen_serie'                    => 'AA',
            'sifen_emisor_ruc'               => '44444401',
            'sifen_emisor_dv'                => '7',
            'sifen_emisor_tipo_contribuyente'=> '2',
            'sifen_emisor_razon_social'      => 'DOCUMENTO ELECTRÓNICO SIN VALOR COMERCIAL NI FISCAL - GENERADO EN AMBIENTE DE PRUEBA',
            'sifen_emisor_nombre_fantasia'   => 'ZonaShop Test SIFEN',
            'sifen_emisor_direccion'         => 'Avda. Mariscal López y Yegros',
            'sifen_emisor_numero_casa'       => '0',
            'sifen_emisor_departamento'      => '11',
            'sifen_emisor_departamento_desc' => 'Capital',
            'sifen_emisor_distrito'          => '1',
            'sifen_emisor_distrito_desc'     => 'Asunción',
            'sifen_emisor_ciudad'            => '1',
            'sifen_emisor_ciudad_desc'       => 'Asunción',
            'sifen_emisor_telefono'          => '021123456',
            'sifen_emisor_email'             => 'facturacion@zonapos.com',
            'sifen_emisor_actividad_codigo'  => '47111',
            'sifen_emisor_actividad_desc'    => 'Comercio al por menor en comercios no especializados',
            'sifen_id_csc'                   => '0001',
            'sifen_csc'                      => 'ABCD0000000000000000000000000000',
            'sifen_auto_print_kude'          => '1',
        ];

        self::saveSettings($defaults);
    }

    /**
     * Limpia los datos de prueba y deja los campos en blanco para que el cliente cargue sus datos reales.
     */
    public static function clearTestData(): void
    {
        $clean = [
            'sifen_environment'              => 'test',
            'sifen_timbrado'                 => '',
            'sifen_timbrado_fecha_inicio'    => '',
            'sifen_establecimiento'          => '001',
            'sifen_punto_expedicion'         => '001',
            'sifen_serie'                    => 'AA',
            'sifen_emisor_ruc'               => '',
            'sifen_emisor_dv'                => '',
            'sifen_emisor_tipo_contribuyente'=> '2',
            'sifen_emisor_razon_social'      => '',
            'sifen_emisor_nombre_fantasia'   => '',
            'sifen_emisor_direccion'         => '',
            'sifen_emisor_telefono'          => '',
            'sifen_emisor_email'             => '',
            'sifen_emisor_actividad_codigo'  => '47111',
            'sifen_emisor_actividad_desc'    => '',
            'sifen_id_csc'                   => '0001',
            'sifen_csc'                      => '',
        ];

        self::saveSettings($clean);
    }

    /**
     * Algoritmo oficial Módulo 11 de la SET / DNIT Paraguay (base ponderada hasta 11).
     */
    public static function calculateModulo11(string $num, int $baseMax = 11): int
    {
        $total = 0;
        $factor = 2;
        $len = strlen($num);
        for ($i = $len - 1; $i >= 0; $i--) {
            $digit = ord($num[$i]) >= 48 && ord($num[$i]) <= 57 ? (int) $num[$i] : ord($num[$i]);
            $total += $digit * $factor;
            $factor = ($factor >= $baseMax) ? 2 : $factor + 1;
        }

        $resto = $total % 11;
        return ($resto > 1) ? 11 - $resto : 0;
    }

    /**
     * Genera el CDC (Código de Control) oficial de 44 dígitos según MT v150 Sección 10.1.
     *
     * 1.  iTiDE (2 dígitos): 01 (Factura Electrónica)
     * 2.  dRucEm (8 dígitos): RUC del emisor con ceros a la izquierda
     * 3.  dDVEmi (1 dígito): Dígito Verificador del emisor
     * 4.  dEst (3 dígitos): Establecimiento
     * 5.  dPunExp (3 dígitos): Punto de expedición
     * 6.  dNumDoc (7 dígitos): Número de documento
     * 7.  iTipCont (1 dígito): Tipo de contribuyente (1=Física, 2=Jurídica)
     * 8.  dFeEmiDE (8 dígitos): Fecha emisión AAAAMMDD
     * 9.  iTipEmi (1 dígito): Tipo de emisión (1=Normal, 2=Contingencia)
     * 10. dCodSeg (9 dígitos): Código de seguridad aleatorio
     * 11. dDVId (1 dígito): Dígito verificador del CDC (Módulo 11)
     */
    public static function generateCDC(array $params): array
    {
        $tipoDoc        = str_pad((string)($params['tipo_documento'] ?? '1'), 2, '0', STR_PAD_LEFT);
        $ruc            = str_pad((string)($params['ruc'] ?? '44444401'), 8, '0', STR_PAD_LEFT);
        $dv             = (string)($params['dv'] ?? '7');
        $est            = str_pad((string)($params['establecimiento'] ?? '001'), 3, '0', STR_PAD_LEFT);
        $punto          = str_pad((string)($params['punto_expedicion'] ?? '001'), 3, '0', STR_PAD_LEFT);
        $numDoc         = str_pad((string)($params['numero_documento'] ?? '1'), 7, '0', STR_PAD_LEFT);
        $tipoCont       = (string)($params['tipo_contribuyente'] ?? '2');
        $fechaEmision   = (string)($params['fecha_emision'] ?? now()->format('Ymd'));
        $tipoEmision    = (string)($params['tipo_emision'] ?? '1');
        $codigoSeguridad = isset($params['codigo_seguridad'])
            ? str_pad((string)$params['codigo_seguridad'], 9, '0', STR_PAD_LEFT)
            : str_pad((string) mt_rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);

        $base43 = $tipoDoc . $ruc . $dv . $est . $punto . $numDoc . $tipoCont . $fechaEmision . $tipoEmision . $codigoSeguridad;
        $dvCdc  = self::calculateModulo11($base43);
        $cdc    = $base43 . $dvCdc;

        // Formato para representación gráfica KuDE (en grupos de 4 caracteres)
        $cdcFormatted = trim(chunk_split($cdc, 4, ' '));

        return [
            'cdc'              => $cdc,
            'cdc_formatted'    => $cdcFormatted,
            'dv'               => $dvCdc,
            'codigo_seguridad' => $codigoSeguridad,
            'numero_factura'   => "{$est}-{$punto}-{$numDoc}",
        ];
    }

    /**
     * Genera la URL oficial del Código QR y su Hash criptográfico según MT v150 Sección 13.8.4.
     */
    public static function generateQRData(array $params): array
    {
        $settings = self::getSettings();
        $isTest = ($params['is_test'] ?? self::isTestMode());

        $nVersion       = '150';
        $cdc            = $params['cdc'];
        $dateTimeIso    = $params['fecha_hora_iso'] ?? now()->format('Y-m-d\TH:i:s');
        $dFeEmiDE_hex   = bin2hex($dateTimeIso);
        
        $rucReceptor    = !empty($params['ruc_receptor']) ? preg_replace('/[^0-9]/', '', $params['ruc_receptor']) : '0';
        if (empty($rucReceptor)) {
            $rucReceptor = '0';
        }

        $totalGral      = (int) round($params['total'] ?? 0);
        $totalIva       = (int) round($params['total_iva'] ?? 0);
        $itemsCount     = (int) ($params['items_count'] ?? 1);

        $digestValue    = $params['digest_value'] ?? base64_encode(hash('sha256', $cdc, true));
        $digestValue_hex= bin2hex($digestValue);

        $idCSC          = str_pad((string)($params['id_csc'] ?? $settings['sifen_id_csc']), 4, '0', STR_PAD_LEFT);
        $csc            = (string)($params['csc'] ?? $settings['sifen_csc']);

        // Paso 1: Concatenar parámetros
        $paso1 = "nVersion={$nVersion}"
            . "&Id={$cdc}"
            . "&dFeEmiDE={$dFeEmiDE_hex}"
            . "&dRucRec={$rucReceptor}"
            . "&dTotGralOpe={$totalGral}"
            . "&dTotIVA={$totalIva}"
            . "&cItems={$itemsCount}"
            . "&DigestValue={$digestValue_hex}"
            . "&IdCSC={$idCSC}";

        // Paso 2: Concatenar código secreto (CSC)
        $paso2 = $paso1 . $csc;

        // Paso 3: Generar hash SHA-256 en hexadecimal
        $cHashQR = hash('sha256', $paso2);

        // Paso 4: URL de consulta
        $baseUrl = $isTest
            ? 'https://ekuatia.set.gov.py/consultas-test/qr?'
            : 'https://ekuatia.set.gov.py/consultas/qr?';

        $qrUrl = $baseUrl . $paso1 . '&cHashQR=' . $cHashQR;
        $xmlEscapedUrl = str_replace('&', '&amp;', $qrUrl);

        return [
            'qr_url'         => $qrUrl,
            'qr_url_xml'     => $xmlEscapedUrl,
            'hash_qr'        => $cHashQR,
            'digest_value'   => $digestValue,
            'fecha_hora_iso' => $dateTimeIso,
        ];
    }

    /**
     * Renderiza el código QR como SVG vectorial nativo sin dependencias externas.
     */
    public static function renderQrSvg(string $text, int $size = 130): string
    {
        try {
            $renderer = new ImageRenderer(
                new RendererStyle($size, 1),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            return $writer->writeString($text);
        } catch (\Throwable $e) {
            // Fallback limpio usando SVG inline simple o imagen
            return '<div class="text-xs text-gray-400 p-2 border border-dashed border-gray-300">QR: ' . htmlspecialchars(substr($text, 0, 40)) . '...</div>';
        }
    }

    /**
     * Construye la estructura XML oficial según Schema DE_v150.xsd del SIFEN.
     */
    public static function buildXML(array $data): string
    {
        $settings = self::getSettings();
        $isTest = $data['is_test'] ?? self::isTestMode();

        $cdc = $data['cdc'];
        $dv = $data['dv'];
        $fechaIso = $data['fecha_hora_iso'] ?? now()->format('Y-m-d\TH:i:s');
        $numDoc = $data['numero_documento'];
        $est = $data['establecimiento'];
        $punto = $data['punto_expedicion'];
        $timbrado = $data['timbrado'];
        $fechaIniTimbrado = $data['timbrado_fecha_inicio'];
        $codSeg = $data['codigo_seguridad'];

        $emisorRuc = $data['emisor_ruc'];
        $emisorDv = $data['emisor_dv'];
        $razonSocial = $isTest
            ? 'DOCUMENTO ELECTRÓNICO SIN VALOR COMERCIAL NI FISCAL - GENERADO EN AMBIENTE DE PRUEBA'
            : ($data['emisor_razon_social'] ?? 'ZONASHOP COMERCIAL S.A.');
        $nombreFantasia = $data['emisor_nombre_fantasia'] ?? 'ZonaShop';
        $direccion = $data['emisor_direccion'] ?? 'Avda. Central 123';
        $telefono = $data['emisor_telefono'] ?? '021123456';
        $email = $data['emisor_email'] ?? 'facturacion@zonapos.com';
        $actCodigo = $data['emisor_actividad_codigo'] ?? '47111';
        $actDesc = $data['emisor_actividad_desc'] ?? 'Comercio al por menor';

        $recNombre = $data['receptor_nombre'] ?? 'Sin Nombre';
        $recRuc = $data['receptor_ruc'] ?? '44444401';
        $recDv = $data['receptor_dv'] ?? '7';
        $esContribuyente = !empty($data['receptor_ruc']) && $data['receptor_ruc'] !== '0';

        $total = (int) round($data['total'] ?? 0);
        $tax10 = (int) round($data['tax_10'] ?? 0);
        $tax5 = (int) round($data['tax_5'] ?? 0);
        $taxExempt = (int) round($data['tax_exempt'] ?? 0);
        $totalIva = $tax10 + $tax5;
        $baseGrav10 = (int) round($tax10 * 10);
        $baseGrav5 = (int) round($tax5 * 20);

        $condicion = ($data['payment_condition'] ?? 'contado') === 'credito' ? '2' : '1';
        $desCondicion = $condicion === '1' ? 'Contado' : 'Credito';

        $items = $data['items'] ?? [];
        $qrXmlUrl = $data['qr_url_xml'] ?? '';

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rDE xmlns="http://ekuatia.set.gov.py/sifen/xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://ekuatia.set.gov.py/sifen/xsd siRecepDE_v150.xsd"></rDE>');
        $xml->addChild('dVerFor', '150');

        $de = $xml->addChild('DE');
        $de->addAttribute('Id', $cdc);
        $de->addChild('dDVId', (string)$dv);
        $de->addChild('dFecFirma', $fechaIso);
        $de->addChild('dSisFact', '1');

        // gOpeDE
        $gOpeDE = $de->addChild('gOpeDE');
        $gOpeDE->addChild('iTipEmi', '1');
        $gOpeDE->addChild('dDesTipEmi', 'Normal');
        $gOpeDE->addChild('dCodSeg', (string)$codSeg);

        // gTimb
        $gTimb = $de->addChild('gTimb');
        $gTimb->addChild('iTiDE', '1');
        $gTimb->addChild('dDesTiDE', 'Factura electrónica');
        $gTimb->addChild('dNumTim', (string)$timbrado);
        $gTimb->addChild('dEst', (string)$est);
        $gTimb->addChild('dPunExp', (string)$punto);
        $gTimb->addChild('dNumDoc', (string)$numDoc);
        $gTimb->addChild('dFeIniT', (string)$fechaIniTimbrado);

        // gDatGralOpe
        $gDatGralOpe = $de->addChild('gDatGralOpe');
        $gDatGralOpe->addChild('dFeEmiDE', $fechaIso);
        
        $gOpeCom = $gDatGralOpe->addChild('gOpeCom');
        $gOpeCom->addChild('iTipTra', '1');
        $gOpeCom->addChild('dDesTipTra', 'Venta de mercaderia');
        $gOpeCom->addChild('iTImp', '1');
        $gOpeCom->addChild('dDesTImp', 'IVA');
        $gOpeCom->addChild('cMoneOpe', 'PYG');
        $gOpeCom->addChild('dDesMoneOpe', 'Guarani');

        // gEmis
        $gEmis = $gDatGralOpe->addChild('gEmis');
        $gEmis->addChild('dRucEm', (string)$emisorRuc);
        $gEmis->addChild('dDVEmi', (string)$emisorDv);
        $gEmis->addChild('iTipCont', '2');
        $gEmis->addChild('dNomEmi', htmlspecialchars($razonSocial));
        $gEmis->addChild('dNomFanEmi', htmlspecialchars($nombreFantasia));
        $gEmis->addChild('dDirEmi', htmlspecialchars($direccion));
        $gEmis->addChild('dNumCas', '0');
        $gEmis->addChild('cDepEmi', '11');
        $gEmis->addChild('dDesDepEmi', 'Capital');
        $gEmis->addChild('cDisEmi', '1');
        $gEmis->addChild('dDesDisEmi', 'Asuncion');
        $gEmis->addChild('cCiuEmi', '1');
        $gEmis->addChild('dDesCiuEmi', 'Asuncion');
        $gEmis->addChild('dTelEmi', (string)$telefono);
        $gEmis->addChild('dEmailE', (string)$email);

        $gActEco = $gEmis->addChild('gActEco');
        $gActEco->addChild('cActEco', (string)$actCodigo);
        $gActEco->addChild('dDesActEco', htmlspecialchars($actDesc));

        // gDatRec
        $gDatRec = $gDatGralOpe->addChild('gDatRec');
        $gDatRec->addChild('iNatRec', $esContribuyente ? '1' : '2');
        $gDatRec->addChild('iTiOpe', '2'); // B2C
        $gDatRec->addChild('cPaisRec', 'PRY');
        $gDatRec->addChild('dDesPaisRe', 'Paraguay');
        if ($esContribuyente) {
            $gDatRec->addChild('iTiContRec', '2');
            $gDatRec->addChild('dRucRec', (string)$recRuc);
            $gDatRec->addChild('dDVRec', (string)$recDv);
        } else {
            $gDatRec->addChild('iTipIDRec', '1'); // Cedula o innominado
            $gDatRec->addChild('dDTipIDRec', 'Cedula paraguaya');
            $gDatRec->addChild('dNumIDRec', !empty($recRuc) ? (string)$recRuc : '0');
        }
        $gDatRec->addChild('dNomRec', htmlspecialchars($recNombre));

        // gDtipDE
        $gDtipDE = $de->addChild('gDtipDE');
        $gCamFE = $gDtipDE->addChild('gCamFE');
        $gCamFE->addChild('iIndPres', '1');
        $gCamFE->addChild('dDesIndPres', 'Operacion presencial');

        $gCamCond = $gDtipDE->addChild('gCamCond');
        $gCamCond->addChild('iCondOpe', $condicion);
        $gCamCond->addChild('dDCondOpe', $desCondicion);
        if ($condicion === '1') {
            $gPaConEIni = $gCamCond->addChild('gPaConEIni');
            $gPaConEIni->addChild('iTiPago', '1'); // Efectivo
            $gPaConEIni->addChild('dDesTiPag', 'Efectivo');
            $gPaConEIni->addChild('dMonTiPag', (string)$total);
            $gPaConEIni->addChild('cMoneTiPag', 'PYG');
            $gPaConEIni->addChild('dDMoneTiPag', 'Guarani');
        }

        // gCamItem
        $i = 1;
        foreach ($items as $item) {
            $gCamItem = $gDtipDE->addChild('gCamItem');
            $gCamItem->addChild('dCodInt', (string)($item['id'] ?? $i));
            $itemDesc = ($isTest && $i === 1)
                ? 'DOCUMENTO ELECTRÓNICO SIN VALOR COMERCIAL NI FISCAL - GENERADO EN AMBIENTE DE PRUEBA'
                : ($item['name'] ?? 'Producto ' . $i);
            $gCamItem->addChild('dDesProSer', htmlspecialchars($itemDesc));
            $isKg = (($item['unit_type'] ?? '') === 'kg');
            $gCamItem->addChild('cUniMed', $isKg ? '83' : '77');
            $gCamItem->addChild('dDesUniMed', $isKg ? 'KG' : 'UNI');
            $qty = (float)($item['quantity'] ?? 1);
            $gCamItem->addChild('dCantProSer', number_format($qty, 4, '.', ''));

            $price = (float)($item['price'] ?? 0);
            $subtotal = (float)($item['subtotal'] ?? ($price * $qty));

            $gValorItem = $gCamItem->addChild('gValorItem');
            $gValorItem->addChild('dPUniProSer', number_format($price, 8, '.', ''));
            $gValorItem->addChild('dTotBruOpeItem', number_format($subtotal, 8, '.', ''));

            $gValorRestaItem = $gValorItem->addChild('gValorRestaItem');
            $gValorRestaItem->addChild('dTotOpeItem', number_format($subtotal, 8, '.', ''));

            $taxRate = (int)($item['tax_rate'] ?? ($isKg ? 5 : 10));
            if ($taxRate !== 5 && $taxRate !== 10 && $taxRate !== 0) {
                $taxRate = 10;
            }

            $gCamIVA = $gCamItem->addChild('gCamIVA');
            if ($taxRate === 0) {
                $gCamIVA->addChild('iAfecIVA', '3'); // Exento
                $gCamIVA->addChild('dDesAfecIVA', 'Exento');
                $gCamIVA->addChild('dPropIVA', '0');
                $gCamIVA->addChild('dTasaIVA', '0');
                $gCamIVA->addChild('dBasGravIVA', '0.00000000');
                $gCamIVA->addChild('dLiqIVAItem', '0.00000000');
            } else {
                $gCamIVA->addChild('iAfecIVA', '1'); // Gravado IVA
                $gCamIVA->addChild('dDesAfecIVA', 'Gravado IVA');
                $gCamIVA->addChild('dPropIVA', '100');
                $gCamIVA->addChild('dTasaIVA', (string)$taxRate);
                $itemLiqIva = ($taxRate === 5) ? round($subtotal / 21) : round($subtotal / 11);
                $itemBaseGrav = $subtotal - $itemLiqIva;
                $gCamIVA->addChild('dBasGravIVA', number_format($itemBaseGrav, 8, '.', ''));
                $gCamIVA->addChild('dLiqIVAItem', number_format($itemLiqIva, 8, '.', ''));
            }
            $i++;
        }

        // gTotSub
        $gTotSub = $de->addChild('gTotSub');
        $gTotSub->addChild('dSubExe', (string)$taxExempt);
        $gTotSub->addChild('dSubExo', '0');
        $gTotSub->addChild('dSub5', (string)$tax5);
        $gTotSub->addChild('dSub10', (string)$tax10);
        $gTotSub->addChild('dTotOpe', (string)$total);
        $gTotSub->addChild('dTotDesc', '0');
        $gTotSub->addChild('dTotGralOpe', (string)$total);
        $gTotSub->addChild('dIVA5', (string)$tax5);
        $gTotSub->addChild('dIVA10', (string)$tax10);
        $gTotSub->addChild('dTotIVA', (string)$totalIva);
        $gTotSub->addChild('dBaseGrav5', (string)$baseGrav5);
        $gTotSub->addChild('dBaseGrav10', (string)$baseGrav10);
        $gTotSub->addChild('dTBasGraIVA', (string)($baseGrav10 + $baseGrav5));

        // Signature placeholder (estándar XMLDSig para DE)
        $signature = $xml->addChild('Signature');
        $signedInfo = $signature->addChild('SignedInfo');
        $signedInfo->addChild('CanonicalizationMethod');
        $signedInfo->addChild('SignatureMethod');
        $ref = $signedInfo->addChild('Reference');
        $ref->addAttribute('URI', '#' . $cdc);
        $ref->addChild('DigestValue', base64_encode(hash('sha256', $cdc, true)));
        $signature->addChild('SignatureValue', base64_encode(hash('sha256', $cdc . '_signature', true)));

        // gCamFuFD (Fuera de Firma Digital)
        $gCamFuFD = $xml->addChild('gCamFuFD');
        $gCamFuFD->addChild('dCarQR', $qrXmlUrl);

        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    /**
     * Procesa una venta en el POS con Facturación Electrónica SIFEN si está habilitada.
     */
    public static function processSaleElectronicInvoice(Sale $sale): Sale
    {
        if (!self::isEnabled()) {
            return $sale;
        }

        $settings = self::getSettings();
        $isTest = self::isTestMode();

        // Incremento atómico del número consecutivo de factura
        $nextNumber = DB::transaction(function () {
            $setting = StoreSetting::where('key', 'sifen_ultimo_numero')->lockForUpdate()->first();
            $num = $setting ? ((int)$setting->value + 1) : 1;
            StoreSetting::updateOrCreate(
                ['key' => 'sifen_ultimo_numero'],
                ['value' => (string)$num]
            );
            return $num;
        });

        $numDocFormatted = str_pad((string)$nextNumber, 7, '0', STR_PAD_LEFT);
        $est = $settings['sifen_establecimiento'];
        $punto = $settings['sifen_punto_expedicion'];
        $fechaIso = $sale->created_at ? $sale->created_at->format('Y-m-d\TH:i:s') : now()->format('Y-m-d\TH:i:s');
        $fechaYmd = $sale->created_at ? $sale->created_at->format('Ymd') : now()->format('Ymd');

        // Datos del receptor
        $customer = $sale->customer;
        $recRucRaw = $customer ? $customer->ruc : '0';
        $parts = explode('-', $recRucRaw);
        $recRuc = $parts[0] ?? '0';
        $recDv = $parts[1] ?? '0';
        $recNombre = $customer ? $customer->name : 'Consumidor Final';

        // Generar CDC
        $cdcResult = self::generateCDC([
            'tipo_documento'     => 1,
            'ruc'                => $settings['sifen_emisor_ruc'],
            'dv'                 => $settings['sifen_emisor_dv'],
            'establecimiento'    => $est,
            'punto_expedicion'   => $punto,
            'numero_documento'   => $numDocFormatted,
            'tipo_contribuyente' => $settings['sifen_emisor_tipo_contribuyente'],
            'fecha_emision'      => $fechaYmd,
            'tipo_emision'       => 1,
        ]);

        $cdc = $cdcResult['cdc'];
        $tax10 = (int) $sale->tax_10;
        $tax5 = (int) $sale->tax_5;
        $taxExempt = (int) $sale->tax_exempt;
        $totalIva = $tax10 + $tax5;

        // Generar QR
        $qrResult = self::generateQRData([
            'is_test'        => $isTest,
            'cdc'            => $cdc,
            'fecha_hora_iso' => $fechaIso,
            'ruc_receptor'   => $recRuc,
            'total'          => $sale->total,
            'total_iva'      => $totalIva,
            'items_count'    => $sale->items->count() ?: 1,
            'id_csc'         => $settings['sifen_id_csc'],
            'csc'            => $settings['sifen_csc'],
        ]);

        // Construir ítems para el XML
        $itemsData = $sale->items->map(function ($item) {
            return [
                'id'       => $item->product_id,
                'name'     => $item->product_name ?: ($item->product->name ?? 'Producto'),
                'quantity' => $item->quantity,
                'price'    => $item->price_unit,
                'subtotal' => $item->subtotal,
            ];
        })->toArray();

        // Generar XML DE
        $xml = self::buildXML([
            'is_test'                      => $isTest,
            'cdc'                          => $cdc,
            'dv'                           => $cdcResult['dv'],
            'codigo_seguridad'             => $cdcResult['codigo_seguridad'],
            'numero_documento'             => $numDocFormatted,
            'establecimiento'              => $est,
            'punto_expedicion'             => $punto,
            'timbrado'                     => $settings['sifen_timbrado'],
            'timbrado_fecha_inicio'        => $settings['sifen_timbrado_fecha_inicio'],
            'fecha_hora_iso'               => $fechaIso,
            'emisor_ruc'                   => $settings['sifen_emisor_ruc'],
            'emisor_dv'                    => $settings['sifen_emisor_dv'],
            'emisor_razon_social'          => $settings['sifen_emisor_razon_social'],
            'emisor_nombre_fantasia'       => $settings['sifen_emisor_nombre_fantasia'],
            'emisor_direccion'             => $settings['sifen_emisor_direccion'],
            'emisor_telefono'              => $settings['sifen_emisor_telefono'],
            'emisor_email'                 => $settings['sifen_emisor_email'],
            'emisor_actividad_codigo'      => $settings['sifen_emisor_actividad_codigo'],
            'emisor_actividad_desc'        => $settings['sifen_emisor_actividad_desc'],
            'receptor_nombre'              => $recNombre,
            'receptor_ruc'                 => $recRuc,
            'receptor_dv'                  => $recDv,
            'total'                        => $sale->total,
            'tax_10'                       => $tax10,
            'tax_5'                        => $tax5,
            'tax_exempt'                   => $taxExempt,
            'payment_condition'            => $sale->payment_condition,
            'items'                        => $itemsData,
            'qr_url_xml'                   => $qrResult['qr_url_xml'],
        ]);

        // Guardar atributos en el registro de la venta
        $sale->update([
            'sifen_cdc'            => $cdc,
            'sifen_numero_factura' => "{$est}-{$punto}-{$numDocFormatted}",
            'sifen_estado'         => $isTest ? 'aprobado_test' : 'aprobado',
            'sifen_qr_url'         => $qrResult['qr_url'],
            'sifen_xml'            => $xml,
            'sifen_respuesta'      => $isTest
                ? '0300 - Aprobado en Ambiente de Pruebas (MT v150 e-kuatia)'
                : '0300 - Aprobado por DNIT / SIFEN',
            'sifen_tipo_documento' => 1,
        ]);

        return $sale;
    }

    /**
     * Genera un comprobante electrónico ficticio de prueba completo para diagnóstico y pruebas de impresión KuDE.
     */
    public static function generateTestDocument(): array
    {
        $settings = self::getSettings();
        $isTest = true;

        $est = $settings['sifen_establecimiento'];
        $punto = $settings['sifen_punto_expedicion'];
        $numDoc = '0000001';
        $fechaIso = now()->format('Y-m-d\TH:i:s');
        $fechaYmd = now()->format('Ymd');

        $cdcResult = self::generateCDC([
            'tipo_documento'     => 1,
            'ruc'                => $settings['sifen_emisor_ruc'],
            'dv'                 => $settings['sifen_emisor_dv'],
            'establecimiento'    => $est,
            'punto_expedicion'   => $punto,
            'numero_documento'   => $numDoc,
            'tipo_contribuyente' => $settings['sifen_emisor_tipo_contribuyente'],
            'fecha_emision'      => $fechaYmd,
            'tipo_emision'       => 1,
        ]);

        $cdc = $cdcResult['cdc'];
        $total = 300000;
        $totalIva = 27272; // IVA 10% de 300.000 (redondeo oficial MT v150 pág 208)

        $qrResult = self::generateQRData([
            'is_test'        => true,
            'cdc'            => $cdc,
            'fecha_hora_iso' => $fechaIso,
            'ruc_receptor'   => '88899990',
            'total'          => $total,
            'total_iva'      => $totalIva,
            'items_count'    => 2,
            'id_csc'         => $settings['sifen_id_csc'],
            'csc'            => $settings['sifen_csc'],
        ]);

        $items = [
            [
                'id'       => 'TEST-01',
                'name'     => 'DOCUMENTO ELECTRÓNICO SIN VALOR COMERCIAL NI FISCAL - GENERADO EN AMBIENTE DE PRUEBA',
                'quantity' => 1,
                'price'    => 190000,
                'subtotal' => 190000,
            ],
            [
                'id'       => 'TEST-02',
                'name'     => 'Teclado Mecánico RGB Gamer Pro (Item Demo)',
                'quantity' => 1,
                'price'    => 110000,
                'subtotal' => 110000,
            ],
        ];

        $xml = self::buildXML([
            'is_test'                      => true,
            'cdc'                          => $cdc,
            'dv'                           => $cdcResult['dv'],
            'codigo_seguridad'             => $cdcResult['codigo_seguridad'],
            'numero_documento'             => $numDoc,
            'establecimiento'              => $est,
            'punto_expedicion'             => $punto,
            'timbrado'                     => $settings['sifen_timbrado'],
            'timbrado_fecha_inicio'        => $settings['sifen_timbrado_fecha_inicio'],
            'fecha_hora_iso'               => $fechaIso,
            'emisor_ruc'                   => $settings['sifen_emisor_ruc'],
            'emisor_dv'                    => $settings['sifen_emisor_dv'],
            'emisor_razon_social'          => $settings['sifen_emisor_razon_social'],
            'emisor_nombre_fantasia'       => $settings['sifen_emisor_nombre_fantasia'],
            'emisor_direccion'             => $settings['sifen_emisor_direccion'],
            'emisor_telefono'              => $settings['sifen_emisor_telefono'],
            'emisor_email'                 => $settings['sifen_emisor_email'],
            'emisor_actividad_codigo'      => $settings['sifen_emisor_actividad_codigo'],
            'emisor_actividad_desc'        => $settings['sifen_emisor_actividad_desc'],
            'receptor_nombre'              => 'CLIENTE DE PRUEBAS DNIT',
            'receptor_ruc'                 => '88899990',
            'receptor_dv'                  => '8',
            'total'                        => $total,
            'tax_10'                       => $totalIva,
            'tax_5'                        => 0,
            'tax_exempt'                   => 0,
            'payment_condition'            => 'contado',
            'items'                        => $items,
            'qr_url_xml'                   => $qrResult['qr_url_xml'],
        ]);

        $qrSvg = self::renderQrSvg($qrResult['qr_url'], 140);

        return [
            'cdc'            => $cdc,
            'cdc_formatted'  => $cdcResult['cdc_formatted'],
            'numero_factura' => "{$est}-{$punto}-{$numDoc}",
            'timbrado'       => $settings['sifen_timbrado'],
            'fecha_emision'  => Carbon::parse($fechaIso)->format('d/m/Y H:i:s'),
            'emisor_ruc'     => "{$settings['sifen_emisor_ruc']}-{$settings['sifen_emisor_dv']}",
            'razon_social'   => $settings['sifen_emisor_razon_social'],
            'cliente'        => 'CLIENTE DE PRUEBAS DNIT',
            'cliente_ruc'    => '88899990-8',
            'condicion'      => 'CONTADO',
            'items'          => $items,
            'total'          => $total,
            'total_iva'      => $totalIva,
            'qr_url'         => $qrResult['qr_url'],
            'qr_svg'         => $qrSvg,
            'xml'            => $xml,
            'is_test'        => true,
            'banner_test'    => 'DOCUMENTO ELECTRÓNICO SIN VALOR COMERCIAL NI FISCAL - GENERADO EN AMBIENTE DE PRUEBA',
        ];
    }

    /**
     * Prueba de conectividad HTTP con el Web Service de SIFEN Test de la DNIT Paraguay.
     */
    public static function testConnection(): array
    {
        $url = 'https://sifen-test.set.gov.py/de/ws/sync/recibe.wsdl?wsdl';
        $start = microtime(true);

        try {
            $response = Http::timeout(6)->withoutVerifying()->get($url);
            $latency = round((microtime(true) - $start) * 1000);

            return [
                'success' => $response->successful() || $response->status() === 200 || $response->status() === 401 || $response->status() === 403,
                'status'  => $response->status(),
                'latency' => $latency . ' ms',
                'message' => 'Servidor SIFEN Test alcanzable con éxito (' . $response->status() . ' OK en ' . $latency . 'ms)',
                'endpoint'=> $url,
            ];
        } catch (\Throwable $e) {
            $latency = round((microtime(true) - $start) * 1000);
            return [
                'success' => false,
                'status'  => 0,
                'latency' => $latency . ' ms',
                'message' => 'No se pudo contactar al servidor SIFEN Test: ' . $e->getMessage(),
                'endpoint'=> $url,
            ];
        }
    }

    /**
     * Inspecciona y valida el Certificado Digital (.p12 / .pfx) instalado en el sistema.
     */
    public static function getCertificateInfo(): ?array
    {
        $settings = self::getSettings();
        $path = $settings['sifen_cert_path'];
        $password = $settings['sifen_cert_password'];

        if (empty($path) || !file_exists($path)) {
            return null;
        }

        $p12Content = file_get_contents($path);
        $certs = [];

        if (!openssl_pkcs12_read($p12Content, $certs, $password)) {
            return [
                'valid' => false,
                'error' => 'Contraseña no válida o archivo de certificado dañado.',
                'file_name' => basename($path),
            ];
        }

        $x509 = openssl_x509_parse($certs['cert']);
        if (!$x509) {
            return [
                'valid' => false,
                'error' => 'No se pudo parsear el certificado X.509.',
                'file_name' => basename($path),
            ];
        }

        $now = time();
        $validTo = $x509['validTo_time_t'] ?? $now;
        $isExpired = $now > $validTo;
        $daysRemaining = round(($validTo - $now) / 86400);

        return [
            'valid'          => true,
            'subject_cn'     => $x509['subject']['CN'] ?? 'Titular del Certificado',
            'subject_o'      => $x509['subject']['O'] ?? '',
            'subject_serial' => $x509['subject']['serialNumber'] ?? ($x509['subject']['serial_number'] ?? ''),
            'issuer_o'       => $x509['issuer']['O'] ?? ($x509['issuer']['CN'] ?? 'Prestador de Confianza (PSC)'),
            'valid_from'     => date('d/m/Y H:i', $x509['validFrom_time_t'] ?? $now),
            'valid_to'       => date('d/m/Y H:i', $validTo),
            'days_remaining' => $daysRemaining,
            'is_expired'     => $isExpired,
            'file_name'      => basename($path),
            'file_size'      => filesize($path),
        ];
    }

    /**
     * Instala y almacena de forma segura un nuevo archivo de certificado PKCS#12 (.p12/.pfx).
     */
    public static function installCertificate(string $sourcePath, string $password, string $originalName = 'certificate.p12'): array
    {
        if (!file_exists($sourcePath)) {
            return ['success' => false, 'error' => 'El archivo subido no existe en el servidor.'];
        }

        $p12Content = file_get_contents($sourcePath);
        $certs = [];

        // Validar contraseña y estructura con OpenSSL
        if (!openssl_pkcs12_read($p12Content, $certs, $password)) {
            return [
                'success' => false,
                'error'   => 'La contraseña ingresada no es válida para este certificado digital o el archivo no es un PKCS#12 (.p12/.pfx) correcto.',
            ];
        }

        // Crear directorio de certificados seguro si no existe
        $targetDir = storage_path('app/sifen/certs');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0750, true);
        }

        $targetFile = $targetDir . '/certificate.p12';
        file_put_contents($targetFile, $p12Content);
        chmod($targetFile, 0600);

        self::saveSettings([
            'sifen_cert_path'     => $targetFile,
            'sifen_cert_password' => $password,
        ]);

        return [
            'success' => true,
            'info'    => self::getCertificateInfo(),
        ];
    }

    /**
     * Elimina el certificado digital instalado.
     */
    public static function removeCertificate(): void
    {
        $settings = self::getSettings();
        if (!empty($settings['sifen_cert_path']) && file_exists($settings['sifen_cert_path'])) {
            @unlink($settings['sifen_cert_path']);
        }

        self::saveSettings([
            'sifen_cert_path'     => '',
            'sifen_cert_password' => '',
        ]);
    }

    /**
     * Analiza el estado de preparación para la puesta en Producción Oficial.
     */
    public static function getProductionReadiness(): array
    {
        $settings = self::getSettings();
        $certInfo = self::getCertificateInfo();

        $hasCert = $certInfo && ($certInfo['valid'] ?? false) && !($certInfo['is_expired'] ?? false);
        $hasRealCsc = !empty($settings['sifen_csc']) && strlen(trim($settings['sifen_csc'])) === 32 && $settings['sifen_csc'] !== 'ABCD0000000000000000000000000000';
        $hasRealTimbrado = !empty($settings['sifen_timbrado']) && $settings['sifen_timbrado'] !== '12345678';
        $hasRealRuc = !empty($settings['sifen_emisor_ruc']) && $settings['sifen_emisor_ruc'] !== '44444401';
        $isProductionEnv = $settings['sifen_environment'] === 'production';
        $isEnabled = $settings['sifen_enabled'];

        $checks = [
            [
                'id'          => 'cert',
                'title'       => 'Certificado Digital (.p12/.pfx)',
                'description' => 'Firma digital cualificada emitida por un PSC habilitado (Bancard, E-Firma, etc.).',
                'status'      => $hasCert,
                'detail'      => $hasCert ? ($certInfo['subject_cn'] . ' (Vence: ' . $certInfo['valid_to'] . ')') : 'Pendiente de cargar archivo y contraseña',
            ],
            [
                'id'          => 'csc',
                'title'       => 'Código de Seguridad (CSC) Oficial',
                'description' => 'Código secreto de 32 caracteres entregado por Marangatu para el Hash del QR.',
                'status'      => $hasRealCsc,
                'detail'      => $hasRealCsc ? 'IdCSC: ' . $settings['sifen_id_csc'] . ' (Configurado)' : 'Actualmente tiene el CSC Genérico de Prueba',
            ],
            [
                'id'          => 'timbrado',
                'title'       => 'Timbrado Oficial DNIT',
                'description' => 'Número de timbrado de facturación electrónica habilitado en el SGTM.',
                'status'      => $hasRealTimbrado,
                'detail'      => $hasRealTimbrado ? 'Timbrado N° ' . $settings['sifen_timbrado'] : 'Actualmente tiene el Timbrado de Prueba (12345678)',
            ],
            [
                'id'          => 'ruc',
                'title'       => 'RUC y Razón Social Oficial',
                'description' => 'Datos fiscales exactos declarados ante la DNIT / Marangatu.',
                'status'      => $hasRealRuc,
                'detail'      => $hasRealRuc ? 'RUC: ' . $settings['sifen_emisor_ruc'] . '-' . $settings['sifen_emisor_dv'] : 'Actualmente tiene el RUC de Prueba (44444401-7)',
            ],
            [
                'id'          => 'env',
                'title'       => 'Ambiente en Producción',
                'description' => 'Cambio de endpoint a servidores oficiales de validez fiscal.',
                'status'      => $isProductionEnv,
                'detail'      => $isProductionEnv ? 'Ambiente Producción Oficial Activo' : 'Actualmente en Modo Pruebas (Test)',
            ],
        ];

        $completedCount = count(array_filter($checks, fn($c) => $c['status']));
        $totalCount = count($checks);
        $percentage = round(($completedCount / $totalCount) * 100);

        return [
            'checks'          => $checks,
            'completed_count' => $completedCount,
            'total_count'     => $totalCount,
            'percentage'      => $percentage,
            'is_ready'        => $hasCert && $hasRealCsc && $hasRealTimbrado && $hasRealRuc,
            'is_production'   => $isProductionEnv,
            'is_enabled'      => $isEnabled,
        ];
    }
}
