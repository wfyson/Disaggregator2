import java.io.*;
import org.apache.pdfbox.pdmodel.*;
import org.apache.pdfbox.util.*;

public class PDFReader
{
	public static void main(String[] args)
	{
		new PDFReader(args[0]);	
	}

	public PDFReader(String path)
	{
		PDDocument pd;
		try
		{
			File input = new File(path); //The PDF file we want to extract...

			pd = PDDocument.load(input);
			int noPages = (pd.getNumberOfPages());
			if(pd.isEncrypted())
			{
				try
				{
					pd.decrypt("");
					pd.setAllSecurityToBeRemoved(true);
				}
				catch (Exception e)
				{
					throw new Exception("The document is encrypted and we can't decrypt it");
				}
			}
				
			PDFTextStripper stripper = new PDFTextStripper("utf-8");
			for(int i = 0; i < noPages; i++)
			{
				stripper.setStartPage(i);
				stripper.setEndPage(i+1);
				String text = stripper.getText(pd);
				text = text.replace("\n", "").replace("\r", "");
				System.out.println(text);
			}
			if(pd != null)
			{
				pd.close();
			}		
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
}
