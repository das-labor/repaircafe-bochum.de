require 'mini_magick'
require 'rubygems'
require 'erubis'
require './_plugins/raw'

module Jekyll

  class ImgPopup < Liquid::Tag
    include TemplateWrapper

    @@id = 0

    TEMPLATE_NAME = 'img_popup.html.erb'

    def initialize(tag_name, markup, tokens)
      args = markup.strip.split(/\s+/, 3)
      raise "Usage: imgpopup path nn% [title]" unless [2, 3].include? args.length

      @path = args[0]
      if args[1] =~ /^(\d+)%$/
        @percent = $1
      else
        raise "Percent #{args[1]} is not of the form 'nn%'"
      end

      template_file = Pathname.new(__FILE__).dirname + TEMPLATE_NAME
      @template = Erubis::Eruby.new(File.open(template_file).read)
      @title = args[2]
      super
    end

    def render(context)
      source = Pathname.new(context.registers[:site].source).expand_path

      # Calculate the full path to the source image.
      image_path = source + @path.sub(%r{^/}, '')

      @@id += 1
      vars = {
        'id'      => @@id.to_s,
        'image'   => @path,
        'title'   => @title
      }

      # Open the source image, and scale it accordingly.
      image = MiniMagick::Image.open(image_path)
      vars['full_width'] = image[:width]
      vars['full_height'] = image[:height]
      image.resize "#{@percent}%"
      vars['scaled_width'] = image[:width]
      vars['scaled_height'] = image[:height]

      safe_wrap(@template.result(vars))
    end
  end
end

Liquid::Template.register_tag('imgpopup', Jekyll::ImgPopup)

