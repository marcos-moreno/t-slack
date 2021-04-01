
<?php 
require_once "postgres.php";   
$codigo = $_GET['codigo']; 
$connect->query("set schema 'erp';");
    $query = " 
    SELECT * FROM  dblink('host=192.168.193.83
                          user=postgres
                          password=[rfhood.?/]
                          dbname=rfhoods390
                          port=65432
                          options=-csearch_path=adempiere ',
    $$ 
    
    SELECT
    
        mp.Value AS Codigo
        ,mp.Name AS Descripcion
        ,COALESCE(LpG.Precio,0) AS ListaPrecio	
        ,COALESCE( round(LpG.Precio * ((SELECT t.Rate FROM C_Tax AS t WHERE t.C_Tax_ID=1000010)/100),2) ,0) AS Iva
        ,LpG.VersionLP
        ,img.Name 				  
        ,encode(img.binarydata, 'base64')  AS file	
      --,img.binarydata
    FROM M_Product AS mp 
            
        LEFT JOIN C_UOM AS u
            ON u.C_UOM_ID = mp.C_UOM_ID
                          
        LEFT JOIN AD_Image AS img
            ON img.AD_Image_ID = mp.Logo_ID
            
        LEFT JOIN LATERAL(
                SELECT
                
                lp3.M_Product_ID
                ,round(COALESCE(lp3.pricelist,0),2) AS Precio
                ,lpv.Name AS VersionLP
                        -- ,lpv.created
                        -- ,lpv.validfrom
                    FROM
                         M_PriceList AS lp
                         ,M_PriceList_Version AS lpv
                         ,M_ProductPrice AS lp3
                    WHERE
                        lp.M_PriceList_ID = lpv.M_PriceList_ID
                        AND lp3.M_PriceList_Version_ID = lpv.M_PriceList_Version_ID 
                        AND lp.M_PriceList_ID= 1000020 -- 1000020 Lista de Precios Principal
                        and lp3.m_product_id = mp.M_Product_ID -- 1005695
                        and lpv.validfrom <= now()::Date  --'2017/07/09'::date
                        AND lpv.Isactive = 'Y' 
                        ORDER BY lpv.created DESC LIMIT 1
            ) AS LpG
            
                ON lpG.M_Product_ID = mp.M_Product_ID		
    
    WHERE
    
        mp.AD_Client_ID = 1000000
        --AND mp.M_Product_ID = 1008842
        AND mp.Value = $$ ||''''||'".$codigo."'||''''|| $$ 
    
                          
    $$ ) 
                         
                         AS pLP(Codigo character varying
                                ,Descripcion character varying
                                ,ListaPrecio Numeric
                                ,Iva Numeric
                                ,NombreLista character varying
                                ,nombre text
                                ,data text
                                  )
    ";
    // $statement = $connect->prepare($query); 
    $data = array();


    foreach ($connect->query($query) as $row) {
        $img = "<img src= 'data:image/jpeg;base64, " . $row["data"] . "' />";
          print($img);
        // var_dump($row["data"]);
    }
    