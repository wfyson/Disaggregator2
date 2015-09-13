import java.io.*;
import org.apache.pdfbox.pdmodel.*;
import org.apache.pdfbox.util.*;

public class PDFPageCount
{
	public static void main(String[] args)
	{
		new PDFPageCount(args[0]);	
	}

	public PDFPageCount(String path)
	{
		try
		{
			File input = new File(path);
			PDDocument pd;
			pd = PDDocument.load(input);
		 	int noPages = (pd.getNumberOfPages());
			System.out.println(noPages);
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
}
