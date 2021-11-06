<?php
namespace Application\Domain\Util\Collection\Render\Helper;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RenderAsPdfHelper
{

    const DEFAULT_CSS = "
        <!-- EXAMPLE OF CSS STYLE -->
        <style>
        
         .docType{
                color: black;
                font-family: times;
                font-weight: 200;
                font-size: 16pt;
                text-decoration: underline;
                margin-bottom:20pt;
            }
        
        
        
        .table-fill {
          background: white;
          border-radius:3px;
          border-collapse: collapse;
          margin: auto;
          max-width: 1600px;
          padding:5px;
          width: 100%;
          box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
          animation: float 5s infinite;
        }
        
        th {
          border-bottom: 1px solid #C1C3D1;
          border-top: 1px solid #C1C3D1;
          background:#1b1e24;
          font-size:12pt;
          font-weight: 100;
          padding:24px;
          text-align:left;
          text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
          vertical-align:middle;
        }
        
        
        
        tr {
          border-top: 1px solid #C1C3D1;
          border-bottom-: 1px solid #C1C3D1;
          color:black;
          font-size:12pt;
          font-weight:normal;
          text-shadow: 0 1px 1px rgba(256, 256, 256, 0.1);
          margin-top:30px;
          margin-bottom:30px;
        
        }
        
        tr:hover td {
          background:#4E5066;
          color:#FFFFFF;
          border-top: 1px solid #22262e;
        }
        
        tr:first-child {
          border-top:none;
        }
        
        tr:last-child {
          border-bottom:none;
        }
        
        tr:nth-child(odd) td {
          background:#EBEBEB;
        }
        
        tr:nth-child(odd):hover td {
          background:#4E5066;
        }
        
        tr:last-child td:first-child {
          border-bottom-left-radius:3px;
        }
        
        tr:last-child td:last-child {
          border-bottom-right-radius:3px;
        }
        
        td {
          background:#FFFFFF;
           text-align:left;
          vertical-align:middle;
          font-weight:100;
          font-size:10pt;
          border-bottom: 1px solid #C1C3D1;
               font-family: times;
        
        }
        
        td:last-child {
          border-right: 0px;
        }
        
        th.text-left {
          text-align: left;
        }
        
        th.text-center {
          text-align: center;
        }
        
        th.text-right {
          text-align: right;
        }
        
        td.text-left {
          text-align: left;
        }
        
        td.text-center {
          text-align: center;
        }
        
        td.text-right {
          text-align: right;
        }
        
        .docDetail{
            color: graytext; text-align: Left;
            padding:0;
            margin:0;
            font-size:9pt;
             font-weight:normal;
         color: black;
               font-family: times;
        
        font-style: italic;
        
        }
        
        .itemDetail{
             color: #4D4B4B ;
             font-size:11pt;
             font-weight:normal;
             font-family: monospace;
             font-style: italic;
             margin-left:9.5pt;
        
        }
        
        
        </style>
        
        ; // END
        ";
}
